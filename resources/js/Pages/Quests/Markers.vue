<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = [
    1, 1, 1, 2
]
const userAnswer = ref({
    1: -1,
    2: -1,
    3: -1,
    4: -1
})

const config = {
    headers: {
        'Authorization': 'Bearer ' + userToken
    }
}

const questCompleted = ref(false)
const checkQuestCompleted = function () {
    axios
        .get('/api/quests/name/markers', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = {1: 1, 2: 1, 3: 1, 4: 2}
                errors.value = ['correct','correct','correct','correct']
            }
        })
}

const errors = ref(['', '', '', ''])

const sendAnswer = function () {
    if (!questCompleted.value) {
        for (let i = 0; i < correctAnswer.length; i++) {
            const key = i + 1
            const val = Number(userAnswer.value[key])
            if (val === -1) {
                errors.value[i] = 'incorrect'
            } else {
                errors.value[i] = (val === Number(correctAnswer[i])) ? 'correct' : 'incorrect'
            }
        }

        const allCorrect = errors.value.every(mark => mark === 'correct')
        if (allCorrect) {
            axios
                .post('/api/quests/markers/complete', {}, config)
                .then((data) => {
                    if (data.data.completed === true) {
                        window.location.reload()
                    }
                })
        }
    }
}


onBeforeMount(() => {
    checkQuestCompleted()
})
</script>

<template>
    <Head title="Вехи истории" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Вехи истории</h1>
            <div class="info-block">
                <p>
                    Вы уже прошли часть выставки и без труда найдете ответы на следующие вопросы.
                </p>
            </div>
        </div>

        <div class="quest">
            <div class="quest__list">
                <div
                    class="quest__list-item"
                    :class="errors[0]"
                >
                    <p class="quest__list-item-question">
                        В каком возрасте Табаков начал педагогическую деятельность?
                    </p>
                    <div class="quest__list-item-answers">
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer1" id="answer1-1" value="1" v-model="userAnswer['1']">
                            <label for="answer1-1">39 лет</label>
                        </div>
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer1" id="answer1-2" value="2" v-model="userAnswer['1']">
                            <label for="answer1-2">29 лет</label>
                        </div>
                    </div>
                </div>
                <div
                    class="quest__list-item"
                    :class="errors[1]"
                >
                    <p class="quest__list-item-question">
                        Что находилось в помещении, где позже был сыгран первый спектакль студии Табакова?
                    </p>
                    <div class="quest__list-item-answers">
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer2" id="answer2-1" value="1" v-model="userAnswer['2']">
                            <label for="answer2-1">Угольный подвал</label>
                        </div>
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer2" id="answer2-2" value="2" v-model="userAnswer['2']">
                            <label for="answer2-2">Архив театра</label>
                        </div>
                    </div>
                </div>
                <div
                    class="quest__list-item"
                    :class="errors[2]"
                >
                    <p class="quest__list-item-question">
                        Что значит принцип «театр-дом» в обучении табаковцев?
                    </p>
                    <div class="quest__list-item-answers">
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer3" id="answer3-1" value="1" v-model="userAnswer['3']">
                            <label for="answer3-1">Атмосфера преемственности</label>
                        </div>
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer3" id="answer3-2" value="2" v-model="userAnswer['3']">
                            <label for="answer3-2">Жесткая иерархия: мастер ученик</label>
                        </div>
                    </div>
                </div>
                <div
                    class="quest__list-item"
                    :class="errors[3]"
                >
                    <p class="quest__list-item-question">
                        В 1986 году Олег Табаков стал ректором:
                    </p>
                    <div class="quest__list-item-answers">
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer4" id="answer4-1" value="1" v-model="userAnswer['4']">
                            <label for="answer4-1">Театрального института имени Щукина</label>
                        </div>
                        <div class="quest__list-item-answers-group">
                            <input type="radio" name="answer4" id="answer4-2" value="2" v-model="userAnswer['4']">
                            <label for="answer4-2">Школы-студии МХАТ</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="quest__send">
                <button class="quest__send-btn" @click="sendAnswer">Ответить</button>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
    .quest {
        &__list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 100px;

            &-item {
                background: radial-gradient(83.46% 83.46% at 50% 50%, rgba(24, 23, 33, 0.2) 36.88%, rgba(108, 101, 137, 0.2) 100%) /* warning: gradient uses a rotation that is not supported by CSS and may not behave as expected */, #1C1A26;
                border-radius: 15px;
                padding: 20px;

                &-question {
                    font-style: normal;
                    font-weight: 600;
                    font-size: 14px;
                    line-height: 130%;
                    color: #FFFFFF;
                    margin-bottom: 10px;
                }

                &-answers {
                    display: flex;
                    justify-content: flex-start;
                    align-items: flex-start;
                    gap: 20px;

                    &-group {
                        input {
                            display: none;

                            &:checked + label {
                                &::before {
                                    border: 1px solid #EFC30A;
                                }
                            }
                        }

                        label {
                            font-style: normal;
                            font-weight: 400;
                            font-size: 12px;
                            line-height: 130%;
                            color: #FFFFFF;
                            display: flex;
                            justify-content: flex-start;
                            align-items: center;

                            &::before {
                                content: '';
                                display: block;
                                width: 12px;
                                height: 12px;
                                border: 1px solid #414141;
                                background-color: #191919;
                                border-radius: 50%;
                                margin-right: 4px;
                                flex-shrink: 0;
                            }
                        }
                    }
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
