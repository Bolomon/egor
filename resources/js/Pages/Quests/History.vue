<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = '3142'
const userAnswer = ref('')

const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

const questCompleted = ref(false)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/history', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = correctAnswer
            }
        })
}

const error = ref(false)

const sendAnswer = function () {
    if (!questCompleted.value) {
        if (userAnswer.value === correctAnswer) {
            axios
                .post('/api/quests/history/complete', {}, config)
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
    <Head title="История жизни О. П. Табакова" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">История жизни<br>О. П. Табакова</h1>
            <div class="info-block">
                <p>
                    Перед вами кадры из жизни Олега Табакова. Нажмите на фотографии в нужном порядке: от самых ранних  к самым поздним.
                </p>
            </div>
        </div>

        <div class="quest">
            <div class="quest__list">
                <div class="quest__list-item">
                    <span>1</span>
                    <img src="/img/quests/history/1.png" alt="1">
                </div>
                <div class="quest__list-item">
                    <span>2</span>
                    <img src="/img/quests/history/2.png" alt="2">
                </div>
                <div class="quest__list-item">
                    <span>3</span>
                    <img src="/img/quests/history/3.png" alt="3">
                </div>
                <div class="quest__list-item">
                    <span>4</span>
                    <img src="/img/quests/history/4.png" alt="4">
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
                    placeholder="Напишите цифры"
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
            grid-template-columns: repeat(2, 1fr);
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
