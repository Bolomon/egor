<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import { onBeforeMount, ref } from "vue";

const userToken = localStorage.getItem("auth-token")
const config = {
  headers: { Authorization: 'Bearer ' + userToken }
}

const getItemClasses = (key, value) => {
  if (questCompleted.value) {
    return {
      correct: Number(value) === Number(correctAnswer[key]),
    }
  }

  const isSelected = Number(userAnswer.value[key]) === Number(value)
  const state = answerChecked.value[key]

  return {
    selected: isSelected,
    correct: isSelected && state === true,
    incorrect: isSelected && state === false,
  }
}

const onChangeAnswer = (key) => {
  if (questCompleted.value) return
  answerChecked.value[key] = null
}

const correctAnswer = {
  1: 0,
  2: 1,
  3: 1,
  4: 0,
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

const checkQuestCompleted = () => {
  axios
    .get('/api/quests/name/achievements', config)
    .then(({ data }) => {
      questCompleted.value = !!data?.completed
      if (questCompleted.value) {
        userAnswer.value = { ...correctAnswer }
        answerChecked.value = { 1: true, 2: true, 3: true, 4: true }
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
      .post('/api/quests/achievements/complete', {}, config)
      .then(({ data }) => {
        if (data?.completed === true) {
          window.location.reload()
        }
      })
  }
}

onBeforeMount(() => {
  checkQuestCompleted()
})
</script>

<template>
    <Head title="Достижения и награды" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Достижения и награды</h1>
            <div class="info-block">
                <p>
                    С актерами всегда так: факты путают, титулы приписывают, заслуги преуменьшают.<br>
                    А что было на самом деле?<br>
                    <br>
                    Перед вами 5 утверждений о Табакове.<br>
                    Отметьте, что из этого правда, а что ложь.
                </p>
            </div>
        </div>

        <div class="quest">
            <h3 class="quest-title">Ваш ответ:</h3>
            <div class="quest__list">
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Снялся<br>
                        в 120 картинах
                    </p>
                    <div class="quest__list-item-answers">
                        <input
                          type="radio"
                          name="1"
                          id="11"
                          value="1"
                          v-model="userAnswer[1]"
                          :disabled="questCompleted"
                          @change="onChangeAnswer(1)"
                        >
                        <label :class="getItemClasses(1, 1)" for="11">Правда</label>

                        <input
                          type="radio"
                          name="1"
                          id="12"
                          value="0"
                          v-model="userAnswer[1]"
                          :disabled="questCompleted"
                          @change="onChangeAnswer(1)"
                        >
                        <label :class="getItemClasses(1, 0)" for="12">Ложь</label>
                    </div>
                </div>
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Почетный гражданин Республики Мордовия
                    </p>
                    <div class="quest__list-item-answers">
                        <input type="radio" name="2" id="21" value="1" v-model="userAnswer[2]" :disabled="questCompleted" @change="onChangeAnswer(2)">
                        <label :class="getItemClasses(2, 1)" for="21">Правда</label>

                        <input type="radio" name="2" id="22" value="0" v-model="userAnswer[2]" :disabled="questCompleted" @change="onChangeAnswer(2)">
                        <label :class="getItemClasses(2, 0)" for="22">Ложь</label>
                    </div>
                </div>
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Почетный доктор чешской Академии изящных искусств
                    </p>
                    <div class="quest__list-item-answers">
                        <input type="radio" name="3" id="31" value="1" v-model="userAnswer[3]" :disabled="questCompleted" @change="onChangeAnswer(3)">
                        <label :class="getItemClasses(3, 1)" for="31">Правда</label>

                        <input type="radio" name="3" id="32" value="0" v-model="userAnswer[3]" :disabled="questCompleted" @change="onChangeAnswer(3)">
                        <label :class="getItemClasses(3, 0)" for="32">Ложь</label>
                    </div>
                </div>
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Не полный кавалер ордена «За заслуги перед Отечеством»
                    </p>
                    <div class="quest__list-item-answers">
                        <input type="radio" name="4" id="41" value="1" v-model="userAnswer[4]" :disabled="questCompleted" @change="onChangeAnswer(4)">
                        <label :class="getItemClasses(4, 1)" for="41">Правда</label>

                        <input type="radio" name="4" id="42" value="0" v-model="userAnswer[4]" :disabled="questCompleted" @change="onChangeAnswer(4)">
                        <label :class="getItemClasses(4, 0)" for="42">Ложь</label>
                    </div>
                </div>
            </div>
            <button class="quest-btn" @click.prevent="sendAnswer">
                {{ questCompleted ? 'Выполнено' : 'Ответить' }}
            </button>
            <div v-if="showErrors && !questCompleted" class="quest-errors">
                Не все ответы верные
            </div>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
.quest {
        margin-top: 80px;

        &-title {
            font-style: normal;
            font-weight: 700;
            font-size: 25px;
            line-height: 34px;
            text-align: center;
            color: #FFFFFF;
            margin-bottom: 20px;
        }

        &__list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 20px;

            &-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;

                &-text {
                    font-style: normal;
                    font-weight: 500;
                    font-size: 14px;
                    line-height: 130%;
                    color: #FFFFFF;
                }

                &-answers {
                    display: grid;
                    grid-template-columns: repeat(2, 90px);
                    gap: 4px;

                    input {
                        display: none;

                        &:checked + label {
                            background: #EFC30A;
                            color: #181721;
                        }
                    }

                    label {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        align-items: center;
                        gap: 10px;
                        width: 90px;
                        height: 40px;
                        background: #242424;
                        border-radius: 25px;
                        font-style: normal;
                        font-weight: 400;
                        font-size: 14px;
                        line-height: 19px;
                        color: rgba(255, 255, 255, 0.5);
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

        &-errors {
            color: #E32636;
            font-size: 16px;
            line-height: 22px;
            text-align: center;
        }
    }
</style>
