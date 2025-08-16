<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";



const userToken = localStorage.getItem("auth-token")

const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

const correctAnswer = {
    1: 1,
    2: 2,
    3: 1
}

const userAnswer = ref({
    1: -1,
    2: -1,
    3: -1,
})

const questCompleted = ref(false)

const step = ref(1)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/person', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = correctAnswer
            }
        })
}

const showErrors = ref(false)

const sendAnswer = function () {
    if (!questCompleted.value) {
        const allCorrect = Object.keys(correctAnswer).every((k) => {
            return Number(userAnswer.value[k]) === Number(correctAnswer[k])
        });

        if (allCorrect) {
            axios
                .post('/api/quests/person/complete', {}, config)
                .then((data) => {
                    if (data.data.completed === true) {
                        window.location.reload()
                    }
                })
        } else {
            showErrors.value = true
        }
    }
}

const setAnswer = function (step, answer) {
    if (!questCompleted.value) {
        userAnswer.value[step] = answer
        showErrors.value = false
    }
}

const nextStep = function () {
    if (userAnswer.value[step.value] !== -1) {
        if (userAnswer.value[step.value] === correctAnswer[step.value]) {
            step.value++
            showErrors.value = false
        } else {
            showErrors.value = true
        }
    }
}

onBeforeMount(() => {
    checkQuestCompleted()
})
</script>

<template>
    <Head title="Грани личности" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Грани личности</h1>
            <div class="info-block">
                <p>
                    Иногда вещи могут многое рассказать<br>
                    о человеке. Если найти правильные вещи.
                </p>
            </div>
        </div>

        <div class="quest">
            <img src="/img/quests/person/main.png" alt="main" class="quest-img">
            <div class="quest__step" v-if="step === 1">
                <img
                    src="/img/quests/person/11.png"
                    alt="11"
                    class="quest__step-img"
                    @click="setAnswer(1, 1)"
                    :class="{selected: userAnswer[1] === 1, correct: questCompleted}"
                >
                <img
                    src="/img/quests/person/12.png"
                    alt="12"
                    class="quest__step-img"
                    @click="setAnswer(1, 2)"
                    :class="{incorrect: showErrors, selected: userAnswer[1] === 2}"
                >
            </div>
            <div class="quest__step" v-else-if="step === 2">
                <img
                    src="/img/quests/person/21.png"
                    alt="21"
                    class="quest__step-img"
                    @click="setAnswer(2, 1)"
                    :class="{incorrect: showErrors, selected: userAnswer[2] === 1}"
                >
                <img
                    src="/img/quests/person/22.png"
                    alt="22"
                    class="quest__step-img"
                    @click="setAnswer(2, 2)"
                    :class="{selected: userAnswer[2] === 2, correct: questCompleted}"
                >
            </div>
            <div class="quest__step" v-else-if="step === 3">
                <img
                    src="/img/quests/person/31.png"
                    alt="31"
                    class="quest__step-img"
                    @click="setAnswer(3, 1)"
                    :class="{selected: userAnswer[3] === 1, correct: questCompleted}"
                >
                <img
                    src="/img/quests/person/32.png"
                    alt="32"
                    class="quest__step-img"
                    @click="setAnswer(3, 2)"
                    :class="{incorrect: showErrors, selected: userAnswer[3] === 2}"
                >
            </div>

            <div class="quest__send">
                <button class="quest__send-btn" @click="sendAnswer" v-if="step >= 3">Ответить</button>
                <button class="quest__send-btn" @click="nextStep" v-else>Ответить</button>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
.quest {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;

    &__step {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;

        &-img {
            width: 100%;
            height: 115px;
            object-fit: cover;
            border-radius: 15px;

            &.selected {
                border: 2px solid #EFC30A;
                box-shadow: inset 0 0 15px #EFC30A;
            }
        }
    }

    &__send {
        position: fixed;
        width: 100%;
        padding: 0 20px;
        height: 80px;
        bottom: 0;
        left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #110F1A;

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
