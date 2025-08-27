<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = [
    'гитис',
    'мхат'
]
const userAnswer = ref([
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
    const c = correctArr.map(normalize)
    const used = new Array(c.length).fill(false)
    const perError = u.map(() => true)

    for (let i = 0; i < u.length; i++) {
        let foundIdx = -1
        for (let j = 0; j < c.length; j++) {
            if (!used[j] && u[i] === c[j]) {
                foundIdx = j
                break
            }
        }
        if (foundIdx !== -1) {
            perError[i] = false
            used[foundIdx] = true
        } else {
            perError[i] = true
        }
    }

    const allMatch = perError.every(e => !e) && u.length === c.length
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
        .get('/api/quests/name/teachers', config)
        .then((data) => {
            questCompleted.value = data.data.completed
            if (questCompleted.value) {
                userAnswer.value = correctAnswer
            }
        })
}

const error = ref([
    false,
    false
])

const sendAnswer = function () {
    if (!questCompleted.value) {
        const { perError, allMatch } = checkAnswersPerField(userAnswer.value, correctAnswer);
        if (allMatch) {
            axios
                .post('/api/quests/teachers/complete', {}, config)
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
    <Head title="Первые учителя" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Первые учителя</h1>
            <div class="info-block">
                <p>
                    Разговор с Орловым поставил юного Олега Табакова перед непростым выбором.<br>
                    Ему нужно было выбрать между 5 и 4 буквами. Что это были за буквы?
                </p>
            </div>
        </div>

        <div class="quest">
            <img
                src="/img/quests/teachers/1.png"
                alt="img"
                class="quest-img"
            >
            <div class="quest__form">
                <p class="quest__form-text">Ваш ответ:</p>
                <div class="quest__form-block">
                    <input
                        type="text"
                        class="quest__form-block-input"
                        :class="{
                            incorrect: error[0],
                            correct: questCompleted,
                        }"
                        placeholder="Слово"
                        v-model="userAnswer[0]"
                        :disabled="questCompleted"
                    >
                    <span>и</span>
                    <input
                        type="text"
                        class="quest__form-block-input"
                        :class="{
                            incorrect: error[1],
                            correct: questCompleted,
                        }"
                        placeholder="Слово"
                        v-model="userAnswer[1]"
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
