<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import { onBeforeMount, ref } from "vue";

const step = ref('intro')

// Обработчик кнопки info-btn
const handleInfoAction = () => {
    if (step.value === 'intro') {
        step.value = 'paper'
        return
    }
    if (step.value === 'paper') {
        step.value = 'congrats'
        return
    }
    if (step.value === 'congrats') {
        // Здесь можно выполнить действие «Забрать подарок», например открыть модал/редирект.
    }
}

// Убираем любые сетевые вызовы — только локальные переключения
onBeforeMount(() => {
// ни
})

</script>

<template>
    <Head title="Постскриптум" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Постскриптум</h1>
            <div v-if="step === 'intro'" class="info-block">
                <p>
                    Эти строки Олег Табаков записал для себя.<br>
                    <br>
                    Они не предназначались для публикации<br>
                    или интервью. Просто личные мысли, размышления о важном.<br>
                    <br>
                    Возможность прочитать их — это возможность заглянуть за кулисы и увидеть еще одну из бесконечного множества граней великого актера, учителя и человека.<br>
                    <br>
                    Спасибо, что прошли этот путь вместе<br>
                    с нами. Пусть вера в то, что солнце обязательно взойдет, никогда не покидает вас. Пока она жива, жив и театр.
                </p>
            </div>
            <div v-if="step === 'paper'" class="info-paper">
                <img src="/img/postscript/postscript.png">
            </div>
            <div v-if="step === 'congrats'" class="info-congratulation">
                <div class="info-congratulation--image">
                    <img src="/img/postscript/firework.png">
                </div>
                <div class="info-congratulation--title">
                    Поздравляем!
                </div>
                <div class="info-congratulation--text">
                    Вы прошли весь квест
                </div>
            </div>
            <button class="info-btn" @click="handleInfoAction">
                {{ step === 'intro' ? 'Смотреть расшифровку' : (step === 'paper' ? 'Завершить' : 'Забрать подарок') }}
            </button>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
.info {
    &-congratulation{
        &--image{
            width: 176px;
            height: 176px;
            background: radial-gradient(83.46% 83.46% at 50% 50%, rgba(24, 23, 33, 0.2) 0%, rgba(108, 101, 137, 0.2) 80%) /* warning: gradient uses a rotation that is not supported by CSS and may not behave as expected */;
            border-radius: 15px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;

            img{
                width: 95px;
                height: 95px;
            }
        }
        &--title{
            font-family: 'Manrope';
            font-style: normal;
            font-weight: 700;
            font-size: 35px;
            line-height: 48px;
            text-align: center;
            color: #FFFFFF;
        }
        &--text{
            font-family: 'Manrope';
            font-style: normal;
            font-weight: 400;
            font-size: 20px;
            line-height: 27px;
            text-align: center;
            color: #FFFFFF;
        }
    }

    &-btn{
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
        margin-top: 80px;
    }
}
</style>
