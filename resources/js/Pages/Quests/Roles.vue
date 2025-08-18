<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import { onBeforeMount, ref } from "vue";

const userToken = localStorage.getItem("auth-token")

const correctAnswer = {
  1: 3,
  2: 4,
  3: 2,
  4: 4,
}

const userAnswer = ref({
  1: -1,
  2: -1,
  3: -1,
  4: -1,
})

const answerChecked = ref({
  1: null,
  2: null,
  3: null,
  4: null,
})

const showErrors = ref(false)
const questCompleted = ref(false)

const config = {
  headers: {
    'Authorization': 'Bearer ' + userToken
  }
}

const checkQuestCompleted = () => {
  axios
    .get('/api/quests/name/roles', config)
    .then(({ data }) => {
      questCompleted.value = !!data?.completed
      if (questCompleted.value) {
        userAnswer.value = { ...correctAnswer }
        answerChecked.value = {
          1: true, 2: true, 3: true, 4: true
        }
      }
    })
}

const validateAnswers = () => {
  const perField = {}
  let allCorrect = true

  Object.keys(correctAnswer).forEach((k) => {
    const key = Number(k)
    const ua = Number(userAnswer.value[key])
    const ca = Number(correctAnswer[key])
    const ok = ua === ca
    perField[key] = ok
    if (!ok) allCorrect = false
  })

  answerChecked.value = perField
  return allCorrect
}

const sendAnswer = () => {
  if (questCompleted.value) return

  const allCorrect = validateAnswers()
  showErrors.value = !allCorrect

  if (allCorrect) {
    axios
      .post('/api/quests/roles/complete', {}, config)
      .then(({ data }) => {
        if (data?.completed === true) {
          window.location.reload()
        }
      })
  }
}

const getSelectClasses = (key) => {
  if (questCompleted.value) {
    return { correct: true }
  }
  const state = answerChecked.value[key]
  return {
    selected: Number(userAnswer.value[key]) !== -1,
    correct: state === true,
    incorrect: state === false,
  }
}

onBeforeMount(() => {
  checkQuestCompleted()
})
</script>

<template>
    <Head title="Роли и образы" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Роли и образы</h1>
            <div class="info-block">
                <p>
                    Перед вами театральные образы Олега Павловича. Но чего-то здесь не хватает?...
                </p>
            </div>
        </div>

        <div class="quest">
            <div class="quest__item">
                <img
                    src="/img/quests/roles/1.png"
                    alt="1"
                    class="quest__item-img"
                >
                <select
                    name="1"
                    id="1"
                    class="quest__item-select"
                    v-model="userAnswer[1]"
                    :disabled="questCompleted"
                    :class="getSelectClasses(1)"
                >
                    <option disabled value="-1">Выберите ответ</option>
                    <option value="1">Стул</option>
                    <option value="2">Часы</option>
                    <option value="3">Зонт</option>
                    <option value="4">Окно</option>
                </select>
            </div>
            <div class="quest__item">
                <img
                    src="/img/quests/roles/2.png"
                    alt="2"
                    class="quest__item-img"
                >
                <select
                    name="2"
                    id="2"
                    class="quest__item-select"
                    v-model="userAnswer[2]"
                    :disabled="questCompleted"
                    :class="getSelectClasses(2)"
                >
                    <option disabled value="-1">Выберите ответ</option>
                    <option value="1">Стол</option>
                    <option value="2">Костюм</option>
                    <option value="3">Шкаф</option>
                    <option value="4">Лестница</option>
                </select>
            </div>
            <div class="quest__item">
                <img
                    src="/img/quests/roles/3.png"
                    alt="3"
                    class="quest__item-img"
                >
                <select
                    name="3"
                    id="3"
                    class="quest__item-select"
                    v-model="userAnswer[3]"
                    :disabled="questCompleted"
                    :class="getSelectClasses(3)"
                >
                    <option disabled value="-1">Выберите ответ</option>
                    <option value="1">Букет</option>
                    <option value="2">Шляпа</option>
                    <option value="3">Перчатки</option>
                    <option value="4">Люстра</option>
                </select>
            </div>
            <div class="quest__item">
                <img
                    src="/img/quests/roles/4.png"
                    alt="4"
                    class="quest__item-img"
                >
                <select
                    name="4"
                    id="4"
                    class="quest__item-select"
                    v-model="userAnswer[4]"
                    :disabled="questCompleted"
                    :class="getSelectClasses(4)"
                >
                    <option disabled value="-1">Выберите ответ</option>
                    <option value="1">Рубашка</option>
                    <option value="2">Шляпа</option>
                    <option value="3">Галстук</option>
                    <option value="4">Подтяжки</option>
                </select>
            </div>
            <button
              class="quest-btn"
              @click="sendAnswer"
            >{{ questCompleted ? 'Выполнено' : 'Ответить' }}</button>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
    .quest {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;

        &__item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;

            &-select {
                width: 100%;
                height: 40px;
                border-radius: 25px;
                background: #242424;
                color: rgba(255, 255, 255, 0.5);
                font-style: normal;
                font-weight: 400;
                font-size: 14px;
                line-height: 19px;
                text-align: center;
                outline: none;
                border: none;
            }
        }

        &-btn {
            grid-column: 1 / 3;
            margin-top: 20px;
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
</style>
