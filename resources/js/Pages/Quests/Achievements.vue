<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import {onBeforeMount, ref} from "vue";

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
                        <input type="radio" name="1" id="11" value="1">
                        <label for="11">Правда</label>

                        <input type="radio" name="1" id="12" value="0">
                        <label for="12">Ложь</label>
                    </div>
                </div>
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Почетный гражданин Республики Мордовия
                    </p>
                    <div class="quest__list-item-answers">
                        <input type="radio" name="2" id="21" value="1">
                        <label for="21">Правда</label>

                        <input type="radio" name="2" id="22" value="0">
                        <label for="22">Ложь</label>
                    </div>
                </div>
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Почетный доктор чешской Академии изящных искусств
                    </p>
                    <div class="quest__list-item-answers">
                        <input type="radio" name="3" id="31" value="1">
                        <label for="31">Правда</label>

                        <input type="radio" name="3" id="32" value="0">
                        <label for="32">Ложь</label>
                    </div>
                </div>
                <div class="quest__list-item">
                    <p class="quest__list-item-text">
                        Не полный кавалер ордена  «За заслуги перед Отечеством»
                    </p>
                    <div class="quest__list-item-answers">
                        <input type="radio" name="4" id="41" value="1">
                        <label for="41">Правда</label>

                        <input type="radio" name="4" id="42" value="0">
                        <label for="42">Ложь</label>
                    </div>
                </div>
            </div>
            <button class="quest-btn">Ответить</button>
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
    }
</style>
