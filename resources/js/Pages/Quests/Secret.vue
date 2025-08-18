<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onMounted, onBeforeUnmount, ref, reactive, nextTick, computed, onBeforeMount} from "vue";

// Регулировки
const DRAG_HEIGHT = 454; // высота поля (была фиксирована в CSS)
const GAP = 6;           // расстояние между зонами
const FRAME_INSET = { top: 20, right: 8, bottom: 100, left: 8 }; // внутренние поля от рамки

const STORAGE_KEY = 'secret-puzzle-v2';

const containerRef = ref(null);

// Слоты по заданной схеме
const slots = ref([]); // [{x,y,w,h,cx,cy}]
const slotsMap = reactive({});

// 11 фрагментов
const pieces = ref(
    Array.from({ length: 11 }, (_, i) => ({
        id: i + 1,
        x: 0,
        y: 0,
        w: 120,
        h: 100,
        slotIndex: null,
        dragging: false,
        correct: null,
        z: 1,
    }))
);

const checked = ref(false);

function desiredSlotIndexForPiece(id) {
    if (id >= 1 && id <= 4) return id - 1; // слоты 0..3
    if (id >= 5 && id <= 8) return id;     // слоты 5..8 (4-й пустой)
    if (id >= 9 && id <= 11) return id;    // слоты 9..11
    return null;
}

// Границы рабочей области с учётом рамки
function getWorkArea() {
    const el = containerRef.value;
    const w = el?.clientWidth ?? 0;
    const h = el?.clientHeight ?? 0;
    const innerW = Math.max(0, w - FRAME_INSET.left - FRAME_INSET.right);
    const innerH = Math.max(0, h - FRAME_INSET.top - FRAME_INSET.bottom);
    return { w, h, innerW, innerH };
}

// Пересчёт слотов
function computeSlots() {
    const el = containerRef.value;
    if (!el) return;

    const { w, innerW, innerH } = getWorkArea();

    // 6 рядов и 5 промежутков
    const rowH = Math.max(1, Math.floor((innerH - GAP * 5) / 6));

    const x0 = FRAME_INSET.left;
    const y0 = FRAME_INSET.top;

    const halfW = Math.floor((innerW - GAP) / 2);
    const thirdW = Math.floor((innerW - GAP * 2) / 3);
    const fullW = innerW;

    const s = [];
    let y = y0;

    // Ряд 1: 1/2, 1/2
    s.push({ x: x0,            y, w: halfW,  h: rowH });
    s.push({ x: x0 + halfW + GAP, y, w: halfW,  h: rowH });
    y += rowH + GAP;

    // Ряд 2: 1/2, 1/2
    s.push({ x: x0,            y, w: halfW,  h: rowH });
    s.push({ x: x0 + halfW + GAP, y, w: halfW,  h: rowH });
    y += rowH + GAP;

    // Ряд 3: 1/1 (пустой)
    s.push({ x: x0,            y, w: fullW,  h: rowH });
    y += rowH + GAP;

    // Ряд 4: 1/2, 1/2
    s.push({ x: x0,            y, w: halfW,  h: rowH });
    s.push({ x: x0 + halfW + GAP, y, w: halfW,  h: rowH });
    y += rowH + GAP;

    // Ряд 5: 1/2, 1/2
    s.push({ x: x0,            y, w: halfW,  h: rowH });
    s.push({ x: x0 + halfW + GAP, y, w: halfW,  h: rowH });
    y += rowH + GAP;

    // Ряд 6: 1/3, 1/3, 1/3
    s.push({ x: x0,                     y, w: thirdW, h: rowH });
    s.push({ x: x0 + thirdW + GAP,      y, w: thirdW, h: rowH });
    s.push({ x: x0 + thirdW * 2 + GAP * 2, y, w: thirdW, h: rowH });

    slots.value = s.map(sl => ({ ...sl, cx: sl.x + sl.w / 2, cy: sl.y + sl.h / 2 }));

    // Подгоним закреплённые фрагменты
    pieces.value.forEach(p => {
        if (p.slotIndex != null && slots.value[p.slotIndex]) {
            const sl = slots.value[p.slotIndex];
            p.x = sl.x; p.y = sl.y;
            p.w = sl.w; p.h = sl.h;
        } else {
            // свободные — в пределах рабочей области
            const minX = FRAME_INSET.left;
            const maxX = FRAME_INSET.left + innerW - p.w;
            const minY = FRAME_INSET.top;
            const maxY = FRAME_INSET.top + innerH - p.h;
            p.x = clamp(p.x, minX, Math.max(minX, maxX));
            p.y = clamp(p.y, minY, Math.max(minY, maxY));
        }
    });
}

function scatterPieces() {
    const { innerW, innerH } = getWorkArea();
    pieces.value.forEach(p => {
        p.w = 120;
        p.h = 100;
        const minX = FRAME_INSET.left;
        const maxX = FRAME_INSET.left + innerW - p.w;
        const minY = FRAME_INSET.top;
        const maxY = FRAME_INSET.top + innerH - p.h;
        p.x = rand(minX, Math.max(minX, maxX));
        p.y = rand(minY, Math.max(minY, maxY));
        p.slotIndex = null;
        p.correct = null;
    });
    Object.keys(slotsMap).forEach(k => delete slotsMap[k]);
}

function snapToNearestSlot(piece) {
    if (!slots.value.length) return;
    const centerX = piece.x + piece.w / 2;
    const centerY = piece.y + piece.h / 2;

    let bestIdx = 0;
    let bestDist = Infinity;
    slots.value.forEach((s, idx) => {
        const dx = s.cx - centerX;
        const dy = s.cy - centerY;
        const d2 = dx * dx + dy * dy;
        if (d2 < bestDist) {
            bestDist = d2;
            bestIdx = idx;
        }
    });

    const occupiedBy = slotsMap[bestIdx];
    if (occupiedBy && occupiedBy !== piece.id) {
        const other = pieces.value.find(x => x.id === occupiedBy);
        if (other) other.slotIndex = null;
    }

    if (piece.slotIndex != null && piece.slotIndex !== bestIdx) {
        delete slotsMap[piece.slotIndex];
    }

    const sl = slots.value[bestIdx];
    piece.x = sl.x; piece.y = sl.y;
    piece.w = sl.w; piece.h = sl.h;
    piece.slotIndex = bestIdx;
    slotsMap[bestIdx] = piece.id;

    if (checked.value) {
        piece.correct = desiredSlotIndexForPiece(piece.id) === bestIdx;
    }
}

let activeId = null;
let dragStart = { left: 0, top: 0, dx: 0, dy: 0 };

function onPointerDown(e, piece) {
    const el = containerRef.value;
    if (!el) return;

    checked.value = false;
    piece.dragging = true;
    piece.z = Date.now();

    const rect = el.getBoundingClientRect();
    dragStart.left = rect.left;
    dragStart.top = rect.top;

    activeId = piece.id;
    e.currentTarget.setPointerCapture?.(e.pointerId);
    dragStart.dx = e.clientX - piece.x - dragStart.left;
    dragStart.dy = e.clientY - piece.y - dragStart.top;

    window.addEventListener('pointermove', onPointerMove, { passive: false });
    window.addEventListener('pointerup', onPointerUp, { passive: false });
}

function onPointerMove(e) {
    if (!activeId) return;
    const el = containerRef.value;
    if (!el) return;

    const p = pieces.value.find(x => x.id === activeId);
    if (!p) return;

    const { innerW, innerH } = getWorkArea();

    const nx = e.clientX - dragStart.left - dragStart.dx;
    const ny = e.clientY - dragStart.top - dragStart.dy;

    const minX = FRAME_INSET.left;
    const maxX = FRAME_INSET.left + innerW - p.w;
    const minY = FRAME_INSET.top;
    const maxY = FRAME_INSET.top + innerH - p.h;

    p.x = clamp(nx, minX, Math.max(minX, maxX));
    p.y = clamp(ny, minY, Math.max(minY, maxY));
    e.preventDefault();
}

function onPointerUp() {
    if (!activeId) return;
    const p = pieces.value.find(x => x.id === activeId);
    if (p) {
        snapToNearestSlot(p);
        p.dragging = false;
        saveState();
    }
    activeId = null;
    window.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('pointerup', onPointerUp);
}

const userToken = localStorage.getItem("auth-token")
const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

function onSubmit() {
    checked.value = true;

    let allCorrect = true;
    const emptyOk = !slotsMap[4]; // слот полной ширины должен быть пуст

    pieces.value.forEach(p => {
        const desired = desiredSlotIndexForPiece(p.id);
        const isCorrect = p.slotIndex === desired;
        p.correct = isCorrect;
        if (!isCorrect) allCorrect = false;
    });

    allCorrect = allCorrect && emptyOk;

    if (allCorrect) {
        axios
            .post('/api/quests/secret/complete', {}, config)
            .then((data) => {
                if (data.data.completed === true) {
                    window.location.reload()
                }
            })
    }
}

const questCompleted = ref(false);

const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/secret', config)
        .then((data) => {
            questCompleted.value = data.data.completed

            if (questCompleted.value) {
                window.location.href = '/postscript'
            }
        })
}

function saveState() {
    try {
        const data = pieces.value.map(p => ({ id: p.id, slotIndex: p.slotIndex }));
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch (_) {}
}

async function loadState() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return false;
        const arr = JSON.parse(raw);
        if (!Array.isArray(arr) || !arr.length) return false;

        Object.keys(slotsMap).forEach(k => delete slotsMap[k]);
        pieces.value.forEach(p => { p.slotIndex = null; p.correct = null; });

        arr.forEach(({ id, slotIndex }) => {
            const p = pieces.value.find(x => x.id === id);
            const sl = slots.value[slotIndex];
            if (p && sl) {
                p.slotIndex = slotIndex;
                p.x = sl.x; p.y = sl.y;
                p.w = sl.w; p.h = sl.h;
                slotsMap[slotIndex] = p.id;
            }
        });

        await nextTick();
        return true;
    } catch (_) {
        return false;
    }
}

function onResize() {
    computeSlots();
}

const countCompletedSteps = ref(12)

const getCountCompletedUserSteps = () => {
    const config = {
        headers: {
            'Authorization': 'Bearer ' + userToken
        }
    }

    axios
        .get('/api/quests/completed/count', config)
        .then((data) => {
            countCompletedSteps.value = data.data.completed_quests_count
        })
}

onMounted(async () => {
    computeSlots();

    const restored = await loadState();
    if (!restored) scatterPieces();

    window.addEventListener('resize', onResize);
    checkQuestCompleted()
    getCountCompletedUserSteps()
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', onResize);
});

// Утилиты
function clamp(v, min, max) { return Math.min(max, Math.max(min, v)); }
function rand(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }
</script>

<template>
    <Head title="Секрет от Олега Павловича" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title" v-if="countCompletedSteps < 11">Вы собрали не все части письма</h1>
            <h1 class="info-title" v-else>Секрет от Олега Павловича</h1>
            <div class="info-block">
                <p v-if="countCompletedSteps < 11">
                    Вы можете вернуться и найти недостающие части
                </p>
                <p v-else>
                    Вы дошли до финала. Если вам удалось собрать все фрагменты письма,
                    вы можете соединить их и прочесть послание от Олега Табакова.
                </p>
            </div>
        </div>

        <div class="quest" v-if="countCompletedSteps >= 11">
            <div
                class="quest__drag"
                ref="containerRef"
                :style="{ '--drag-height': DRAG_HEIGHT + 'px' }"
            >
                <img
                    v-for="p in pieces"
                    :key="p.id"
                    class="piece"
                    :src="`/img/quests/secret/${p.id}.png`"
                    :alt="String(p.id)"
                    :style="{
                        left: p.x + 'px',
                        top: p.y + 'px',
                        width: p.w + 'px',
                        height: p.h + 'px',
                        zIndex: p.z,
                        borderColor: checked ? (p.correct ? '#00FF2F' : '#FF0000') : 'transparent'
                    }"
                    @pointerdown="(e) => onPointerDown(e, p)"
                    draggable="false"
                />
            </div>

            <button class="quest-btn" @click="onSubmit">{{ questCompleted ? 'Выполнено' : 'Ответить' }}</button>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
.quest {
    &__drag {
        position: relative;
        width: 100%;
        height: var(--drag-height); /* высота вынесена в переменную */
        background-image: url('/img/quests/secret/background.png');
        background-size: cover;
        background-position: center;
        margin-bottom: 20px;
        overflow: hidden;
    }

    &-btn {
        background-color: #EFC30A;
        border-radius: 25px;
        height: 40px;
        width: 100%;
        text-align: center;
        border: none;
        outline: none;
        cursor: pointer;
        font-style: normal;
        font-weight: 600;
        font-size: 14px;
        line-height: 19px;
        color: #000000;
    }
}

.piece {
    position: absolute;
    object-fit: contain;
    border-radius: 8px;
    user-select: none;
    touch-action: none;
    cursor: grab;
    border: 2px solid transparent;
    box-sizing: border-box;
    transition: border-color .15s ease;

    &:active {
        cursor: grabbing;
    }
}
</style>
