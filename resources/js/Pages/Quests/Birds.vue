<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = [
    'попугай',
    'соловей',
    ['галка', 'галчонок'],
    'ласточка',
]
const userAnswer = ref([
    '',
    '',
    '',
    ''
])

const normalize = (str) => (str ?? '')
    .toString()
    .trim()
    .toLowerCase()
    .replace(/\s+/g, ' ')

const checkAnswersPerField = (userArr, correctArr) => {
    const u = userArr.map(normalize)
    const perError = u.map((val, i) => {
        const c = correctArr[i]
        if (Array.isArray(c)) {
            return !c.map(normalize).includes(val)
        }
        return normalize(c) !== val
    })
    const allMatch = perError.every(e => !e)
    return { perError, allMatch }
}

const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

const questCompleted = ref(false)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/birds', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = correctAnswer.map(c => Array.isArray(c) ? c[0] : c)
            }
        })
}

const error = ref([
    false,
    false,
    false,
    false,
])

const sendAnswer = function () {
    if (!questCompleted.value) {
        const { perError, allMatch } = checkAnswersPerField(userAnswer.value, correctAnswer);
        if (allMatch) {
            axios
                .post('/api/quests/birds/complete', {}, config)
                .then((data) => {
                    if (data.data.completed === true) {
                        window.location.reload()
                    }
                })
        } else {
            error.value = perError
        }
    }
}

onBeforeMount(() => {
    checkQuestCompleted()
})
</script>

<template>
    <Head title="Птицы счастья" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Птицы счастья</h1>
            <div class="info-block">
                <p>
                    Говорят, птицы принесли счастье и спрятались где-то здесь, среди декораций. Найдите всех<br>
                    и впишите их названия.
                </p>
            </div>
        </div>
        <div class="quest">
            <div class="quest__form">
                <p class="quest__form-text">Ваш ответ:</p>
                <div class="quest__form-block">
                    <div class="quest__form-block-title">
                        Мэри Поппинс
                    </div>
                    <input
                        type="text"
                        class="quest__form-block-input"
                        :class="{
                            incorrect: error[0],
                            correct: questCompleted,
                        }"
                        placeholder="Напишите название"
                        v-model="userAnswer[0]"
                        :disabled="questCompleted"
                    >
                </div>
                <div class="quest__form-block">
                    <div class="quest__form-block-title">
                        Обломов
                    </div>
                    <input
                        type="text"
                        class="quest__form-block-input"
                        :class="{
                            incorrect: error[1],
                            correct: questCompleted,
                        }"
                        placeholder="Напишите название"
                        v-model="userAnswer[1]"
                        :disabled="questCompleted"
                    >
                </div>
                <div class="quest__form-block">
                    <div class="quest__form-block-title">
                        Простоквашино
                    </div>
                    <input
                        type="text"
                        class="quest__form-block-input"
                        :class="{
                            incorrect: error[2],
                            correct: questCompleted,
                        }"
                        placeholder="Напишите название"
                        v-model="userAnswer[2]"
                        :disabled="questCompleted"
                    >
                </div>
                <div class="quest__form-block">
                    <div class="quest__form-block-title">
                        Человек с бульвара Капуцинов
                    </div>
                    <input
                        type="text"
                        class="quest__form-block-input"
                        :class="{
                            incorrect: error[3],
                            correct: questCompleted,
                        }"
                        placeholder="Напишите название"
                        v-model="userAnswer[3]"
                        :disabled="questCompleted"
                    >
                </div>
                <button
                    class="quest__form-btn"
                    @click="sendAnswer"
                >Ответить</button>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
.quest {
    &__form {
        margin-top: 100px;
        &-text {
            font-style: normal;
            font-weight: 700;
            font-size: 25px;
            line-height: 34px;
            text-align: center;
            color: #FFFFFF;
            margin-bottom: 15px;
        }

        &-block {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;

            &-input {
                outline: none;
                height: 40px;
                width: 100%;
                border: 1px solid #1C1C1C;
                background-color: #242424;
                border-radius: 25px;
                text-align: center;
                font-size: 14px;
                line-height: 14px;
                color: #FFFFFF;
            }

            span {
                font-style: normal;
                font-weight: 700;
                font-size: 25px;
                line-height: 34px;
                text-align: center;
                color: #FFFFFF;

            }

            &-title {

                width: 328px;
                height: 18px;

                font-family: 'Manrope';
                font-style: normal;
                font-weight: 500;
                font-size: 14px;
                line-height: 130%;
                color: #FFFFFF;
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
}
</style>
