<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = [4,6]
const userAnswer = ref([])

const arraysEqualIgnoreOrder = (a, b) => {
    if (!Array.isArray(a) || !Array.isArray(b)) return false
    if (a.length !== b.length) return false
    const an = a.map(Number).slice().sort((x, y) => x - y)
    const bn = b.map(Number).slice().sort((x, y) => x - y)
    for (let i = 0; i < an.length; i++) {
        if (an[i] !== bn[i]) return false
    }
    return true
}

const checkAnswersPerField = (userArr, correctArr) => {
    const u = userArr.map(Number)
    const c = correctArr.map(Number)

    const counts = new Map()
    for (const v of c) counts.set(v, (counts.get(v) || 0) + 1)

    const perCorrect = u.map((val) => {
        const left = counts.get(val) || 0
        if (left > 0) {
            counts.set(val, left - 1)
            return true
        }
        return false
    });

    const allMatch = arraysEqualIgnoreOrder(u, c)
    return { perCorrect, allMatch }
}

const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

const questCompleted = ref(false)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/library', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = correctAnswer
            }
        })
}

const answerChecked = ref([])

const sendAnswer = function () {
    if (!questCompleted.value) {
        const { perCorrect, allMatch } = checkAnswersPerField(userAnswer.value, correctAnswer)
        answerChecked.value = perCorrect

        if (allMatch) {
            axios
                .post('/api/quests/library/complete', {}, config)
                .then((data) => {
                    if (data.data.completed === true) {
                        window.location.reload()
                    }
                })
        }
    }
}

const addOrRemoveAnswer = function (index) {
    if (questCompleted.value) return

    const arr = userAnswer.value
    const pos = arr.indexOf(index)

    if (pos === -1 && arr.length < 2) {
        userAnswer.value = [...arr, index]
    } else {
        userAnswer.value = [...arr.slice(0, pos), ...arr.slice(pos + 1)]
    }

    answerChecked.value = []
}

const getItemClasses = (key) => {
    const idx = userAnswer.value.indexOf(key)

    if (questCompleted.value) {
        return {correct: idx !== -1}
    }

    return {
        selected: idx !== -1,
        correct: idx !== -1 && answerChecked.value[idx] === true,
        incorrect: idx !== -1 && answerChecked.value[idx] === false,
    }
}

onBeforeMount(() => {
    checkQuestCompleted()
})
</script>

<template>
    <Head title="История жизни О. П. Табакова" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Библиотека</h1>
            <div class="info-block">
                <p>
                    Эти книги на полке у Олега Табакова были <br>
                    в числе любимых. Все, кроме 2.<br>
                    Найдите лишние книги.<br>
                </p>
            </div>
        </div>
        <div class="quest">
            <div class="quest">
                <div class="quest__list">
                    <div
                        class="quest__list-item"
                        @click="addOrRemoveAnswer(1)"
                        :class="getItemClasses(1)"
                    >
                        <img
                            src="/img/quests/library/1.png"
                            alt="1"
                        >
                    </div>
                    <div
                        class="quest__list-item"
                        @click="addOrRemoveAnswer(2)"
                        :class="getItemClasses(2)"
                    >
                        <img
                            src="/img/quests/library/2.png"
                            alt="2"
                        >
                    </div>
                    <div class="quest__list-item"
                         @click="addOrRemoveAnswer(3)"
                         :class="getItemClasses(3)"
                    >
                        <img
                            src="/img/quests/library/3.png"
                            alt="3"
                        >
                    </div>
                    <div
                        class="quest__list-item"
                        @click="addOrRemoveAnswer(4)"
                        :class="getItemClasses(4)"
                    >
                        <img
                            src="/img/quests/library/4.png"
                            alt="4"
                        >
                    </div>
                    <div
                        class="quest__list-item"
                        @click="addOrRemoveAnswer(5)"
                        :class="getItemClasses(5)"
                    >
                        <img
                            src="/img/quests/library/5.png"
                            alt="5"
                        >
                    </div>
                    <div
                        class="quest__list-item"
                        @click="addOrRemoveAnswer(6)"
                        :class="getItemClasses(6)"
                    >
                        <img
                            src="/img/quests/library/6.png"
                            alt="6"
                        >
                    </div>
                </div>
                <div class="quest__form">
                    <button
                        class="quest__form-btn"
                        @click="sendAnswer"
                    >Ответить</button>
                </div>
            </div>
        </div>

    </MainLayout>
</template>

<style scoped lang="scss">
.quest {
    &__list {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 10px;
        margin-bottom: 20px;

        &-item {
            position: relative;
            border-radius: 10px;

            &.selected {
                border: 2px solid #EFC30A;
                box-shadow: inset 0 0 15px #EFC30A;
            }
        }
    }

    &__form {
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
}
</style>

