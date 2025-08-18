<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import { nextTick, onBeforeMount, onBeforeUnmount, onMounted, reactive, ref, watch } from "vue";
import axios from 'axios';

const userToken = localStorage.getItem("auth-token");
const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
};

// Данные
const items = reactive([
    { title: '«Слеза Москвы»', img: '/img/quests/cinema/1.png' },
    { title: '«Молочный запад»', img: '/img/quests/cinema/2.png' },
    { title: '«Механическое пианино»', img: '/img/quests/cinema/3.png' },
    { title: '«Моя звезда»', img: '/img/quests/cinema/4.png' },
    { title: '«Кадриль»', img: '/img/quests/cinema/5.png' },
]);

// Правильные ответы: индекс текста -> индекс картинки (0-базная нумерация)
const correctMapping = [1, 2, 4, 3, 0];

// Ссылки на элементы
const containerRef = ref(null);
const textRefs = ref([]);
const imageRefs = ref([]);
const setTextRef = (el, i) => { if (el) textRefs.value[i] = el; };
const setImageRef = (el, i) => { if (el) imageRefs.value[i] = el; };

// Состояние выбора и соединений
const pending = ref(null); // индекс выбранного текста или null
const selectedTextIndex = ref(null);

// Флаг "проверено"
const checked = ref(false);

// Safari: детект и фолбэк
const isSafari = typeof navigator !== 'undefined'
    && /Safari/.test(navigator.userAgent)
    && !/Chrome|Chromium|Edg|OPR/.test(navigator.userAgent);

// Если Safari — используем фиксированные маркеры ('arrow-yellow'/'-green'/'-red')
// иначе — один маркер с context-stroke
const useFixedMarkers = ref(isSafari);

// Отображение линий
// Каждая линия: { x1, y1, x2, y2, color, marker }
const lines = ref([]);

// Биекция: текст -> картинка и картинка -> текст
const mapping = ref({}); // { [textIndex: number]: imageIndex: number }
const reverse = ref({}); // { [imageIndex: number]: textIndex: number }

// Ключ для localStorage
const STORAGE_KEY = 'cinema-mapping-v1';

// Подсчёт координат линий (SVG fixed на окне)
function computeLines() {
    const result = [];
    for (const [tStr, imgIdx] of Object.entries(mapping.value)) {
        const t = Number(tStr);
        const fromEl = textRefs.value[t];
        const toEl = imageRefs.value[imgIdx];
        if (!fromEl || !toEl) continue;

        const a = fromEl.getBoundingClientRect();
        const b = toEl.getBoundingClientRect();

        const x1 = a.right + 6;
        const y1 = a.top + a.height / 2;

        const x2 = b.left - 6;
        const y2 = b.top + b.height / 2;

        let color = '#EFC30A';
        let marker = useFixedMarkers.value ? 'arrow-yellow' : 'arrow';

        if (checked.value) {
            const isCorrect = correctMapping[t] === imgIdx;
            color = isCorrect ? '#00FF2F' : '#FF0000';
            marker = useFixedMarkers.value
                ? (isCorrect ? 'arrow-green' : 'arrow-red')
                : 'arrow';
        }

        result.push({ x1, y1, x2, y2, color, marker });
    }
    lines.value = result;
}

// Обработчики кликов
function onTextClick(i) {
    if (questCompleted.value) return;
    pending.value = i;
    selectedTextIndex.value = i;
}

async function onImageClick(i) {
    if (questCompleted.value) return;
    if (pending.value == null) return;

    const t = pending.value;

    // 1) Если картинка уже занята другим текстом — освободим её
    if (reverse.value[i] != null) {
        const oldText = reverse.value[i];
        delete mapping.value[oldText];
        delete reverse.value[i];
    }

    // 2) Если у текста уже была картинка — освободим её
    if (mapping.value[t] != null) {
        const oldImg = mapping.value[t];
        delete reverse.value[oldImg];
        delete mapping.value[t];
    }

    // 3) Создаём новую связь t -> i
    mapping.value[t] = i;
    reverse.value[i] = t;

    // Сброс выбора
    pending.value = null;
    selectedTextIndex.value = null;

    await nextTick();
    computeLines();
    saveMapping();
}

// “Запоминание” в localStorage
function saveMapping() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(mapping.value));
    } catch (_) {}
}

async function loadMapping() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return;
        const parsed = JSON.parse(raw);
        // Восстановим mapping и reverse
        mapping.value = {};
        reverse.value = {};
        Object.entries(parsed).forEach(([tStr, imgIdx]) => {
            const t = Number(tStr);
            if (items[t] && items[imgIdx]) {
                mapping.value[t] = imgIdx;
                reverse.value[imgIdx] = t;
            }
        });
        await nextTick();
        computeLines();
    } catch (_) {}
}

// Пересчёт при изменении размера/скролле
function handleReflow() {
    computeLines();
}

onMounted(async () => {
    // Пересчёт при загрузке картинок
    imageRefs.value.forEach(img => {
        if (!img) return;
        if (img.complete) return;
        img.addEventListener('load', handleReflow, { once: true });
        img.addEventListener('error', handleReflow, { once: true });
    });

    await nextTick();
    await loadMapping();

    computeLines();
    window.addEventListener('resize', handleReflow);
    window.addEventListener('scroll', handleReflow, true);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleReflow);
    window.removeEventListener('scroll', handleReflow, true);
});

// Если пользователь меняет связи после проверки — сбрасываем подсветку к нейтральной
watch(mapping, () => {
    if (questCompleted.value) return;
    checked.value = false;
    computeLines();
    saveMapping();
}, { deep: true });

// Проверка по кнопке + отправка запроса при 5/5
function onSubmit() {
    checked.value = true;
    computeLines();

    // Проверяем, что есть ровно 5 связей и все верные
    const sizeOk = Object.keys(mapping.value).length === items.length;
    const allCorrect = sizeOk && items.every((_, t) => mapping.value[t] === correctMapping[t]);

    if (allCorrect && !questCompleted.value) {
        axios
            .post('/api/quests/cinema/complete', {}, config)
            .then((data) => {
                if (data?.data?.completed === true) {
                    window.location.reload();
                }
            })
            .catch((e) => {
                console.error('Ошибка при отправке результата:', e);
            });
    }
}

const questCompleted = ref(false)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/cinema', config)
        .then((data) => {
            if (data.data.completed) {
                questCompleted.value = data.data.completed
                onSubmit()
            }
        })
}

onBeforeMount(() => {
    checkQuestCompleted()
})
</script>

<template>
    <Head title="Кинококтейль Табакова" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Кинококтейль Табакова</h1>
            <div class="info-block">
                <p>
                    Перед вами коктейльная карта и стоп-кадры<br>
                    из 5 фильмов, в которых снимался Олег Табаков.<br><br>
                    У каждого напитка — свой характер.<br>
                    У каждого фильма — своя атмосфера.<br>
                    Сопоставьте коктейли и кадры: каким из фильмов вдохновлен каждый из напитков?
                </p>
            </div>
        </div>

        <div class="quest" ref="containerRef">
            <!-- SVG-оверлей для стрелок (fixed, поверх окна) -->
            <svg class="connector-overlay" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <!-- Универсальный маркер для браузеров с поддержкой context-stroke -->
                    <marker id="arrow" viewBox="0 0 10 10"
                            refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                        <path d="M 0 0 L 10 5 L 0 10 z"
                              fill="context-stroke" stroke="context-stroke" />
                    </marker>

                    <!-- Фиксированные маркеры для Safari (жёлтый/зелёный/красный) -->
                    <marker id="arrow-yellow" viewBox="0 0 10 10"
                            refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#EFC30A" />
                    </marker>
                    <marker id="arrow-green" viewBox="0 0 10 10"
                            refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#00FF2F" />
                    </marker>
                    <marker id="arrow-red" viewBox="0 0 10 10"
                            refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                        <path d="M 0 0 L 10 5 L 0 10 z" fill="#FF0000" />
                    </marker>
                </defs>

                <g>
                    <line
                        v-for="(ln, idx) in lines"
                        :key="`${idx}-${ln.marker}-${ln.color}`"
                        :x1="ln.x1" :y1="ln.y1" :x2="ln.x2" :y2="ln.y2"
                        :stroke="ln.color" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        :marker-end="`url(#${ln.marker})`" />
                </g>
            </svg>

            <div v-for="(item, i) in items" :key="i" class="quest__list">
                <div class="quest__list-item">
                    <p
                        :class="{ 'is-selected': selectedTextIndex === i }"
                        :ref="el => setTextRef(el, i)"
                        @click="onTextClick(i)"
                        tabindex="0"
                        role="button"
                        aria-label="Выбрать текст для связи"
                    >
                        {{ item.title }}
                    </p>
                    <img
                        :src="item.img" :alt="String(i + 1)"
                        :ref="el => setImageRef(el, i)"
                        @click="onImageClick(i)"
                        role="button"
                        aria-label="Выбрать изображение для связи"
                    >
                </div>
            </div>

            <button class="quest-btn" @click="onSubmit">{{ questCompleted ? 'Выполнено' : 'Ответить' }}</button>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
.quest {
    position: relative;

    &__list {
        display: grid;
        grid-template-columns: 1fr;
        margin-bottom: 40px;

        &-item {
            display: grid;
            grid-template-columns: 210px 1fr;
            column-gap: 12px;

            p {
                position: relative;
                padding-left: 16px;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                text-align: left;
                height: 90px;
                background: radial-gradient(83.46% 83.46% at 50% 50%, rgba(24, 23, 33, 0.2) 0%, rgba(108, 101, 137, 0.2) 80%);
                border-radius: 15px;
                font-style: normal;
                font-weight: 500;
                font-size: 14px;
                line-height: 19px;
                color: #FFFFFF;
                cursor: pointer;
                outline: none;
                transition: box-shadow .15s ease;

                &::after {
                    right: -7px;
                    top: 50%;
                    content: '';
                    transform: translateY(-50%);
                    position: absolute;
                    width: 15px;
                    height: 15px;
                    border-radius: 50%;
                    background: #191919;
                    border: 1px solid #414141;
                }

                &.is-selected {
                    box-shadow: 0 0 0 2px #EFC30A inset;
                }
            }

            img {
                justify-self: end;
                align-self: center;
                width: 70px;
                height: 70px;
                object-fit: cover;
                border-radius: 15px;
                cursor: pointer;
                transition: box-shadow .15s ease;

                &:hover {
                    box-shadow: 0 0 0 2px #EFC30A;
                }
            }
        }
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

/* SVG фиксированный на весь экран */
.connector-overlay {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 999;
}
</style>
