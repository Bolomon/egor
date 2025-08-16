<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = ['педагогика', 'педагогики']
const userAnswer = ref('')

const normalize = (s) => (s ?? '').toString().trim().toLowerCase()

const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

const questCompleted = ref(false)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/formula', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = correctAnswer[0]
            }
        })
}

const error = ref(false)

const sendAnswer = function () {
    if (!questCompleted.value) {
        const isCorrect = correctAnswer.map(normalize).includes(normalize(userAnswer.value))
        if (isCorrect) {
            axios
                .post('/api/quests/formula/complete', {}, config)
                .then((data) => {
                    if (data.data.completed === true) {
                        window.location.reload()
                    }
                })
        } else {
            error.value = true
        }
    }
}

onBeforeMount(() => {
    checkQuestCompleted()
})
</script>

<template>
    <Head title="Формула от Олега Павловича" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Формула<br>от Олега Павловича</h1>
            <div class="info-block">
                <p>
                    Формула проста:<br>
                    время + душа + здоровье + жизнь<br>
                    Секрет чего зашифрован в этой формуле?
                </p>
            </div>
        </div>

        <div class="quest">
            <div class="quest__list">
                <div class="quest__list-item">
                    <img src="/img/quests/formula/1.png" alt="1">
                </div>
            </div>
            <div class="quest__form">
                <p class="quest__form-text">Ваш ответ:</p>
                <input
                    type="text"
                    class="quest__form-input"
                    :class="{
                        incorrect: error,
                        correct: questCompleted,
                    }"
                    inputmode="numeric"
                    placeholder="Что за секрет?"
                    v-model="userAnswer"
                    :disabled="questCompleted"
                >
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
    &__list {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 10px;
        margin-bottom: 25px;

        &-item {
            position: relative;

            span {
                display: flex;
                justify-content: center;
                align-items: center;
                position: absolute;
                left: 5px;
                top: 5px;
                width: 16px;
                height: 16px;
                font-style: normal;
                font-weight: 700;
                font-size: 8px;
                line-height: 100%;
                text-align: center;
                color: #000000;
                background-color: #EFC30A;
                border-radius: 50%;
            }
        }
    }

    &__form {
        &-text {
            font-style: normal;
            font-weight: 700;
            font-size: 25px;
            line-height: 34px;
            text-align: center;
            color: #FFFFFF;
            margin-bottom: 15px;
        }

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
            margin-bottom: 20px;
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

