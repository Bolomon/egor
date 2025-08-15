<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import {ref} from "vue";

const userToken = localStorage.getItem("auth-token");

const registerData = ref({
    name: '',
    phone: '',
    agree: false
})
const registerErrors = ref(false)

const sendRegisterForm = function () {
    if (registerData.value.agree) {
        axios
            .post('/api/register', {
                name: registerData.value.name,
                phone: registerData.value.phone,
            })
            .then((data) => {
                const resp = data.data

                localStorage.setItem('auth-token', resp.token);
                window.location.reload()
            })
            .catch((err) => {
                registerErrors.value = err.response.data.errors
            })
    } else {
        registerErrors.value = {agree: 'Необходимо принять соглашения'}
    }
}
</script>

<template>
    <Head title="Главная" />
    <MainLayout>
        <div class="auth" v-if="!userToken">
            <h3 class="auth-title">Регистрация</h3>
            <div class="auth__group">
                <input
                    class="auth__group-input"
                    type="text"
                    placeholder="Ваше имя"
                    v-model="registerData.name"
                >
                <p
                    class="auth__group-error"
                    v-if="registerErrors?.name"
                >
                    {{ registerErrors.name[0] }}
                </p>
            </div>
            <div class="auth__group">
                <input
                    class="auth__group-input"
                    type="tel"
                    inputmode="tel"
                    placeholder="+7 (999) 999-99-99"
                    v-model="registerData.phone"
                >
                <p
                    class="auth__group-error"
                    v-if="registerErrors?.phone"
                >
                    {{ registerErrors.phone[0] }}
                </p>
            </div>
            <div class="auth__checkbox">
                <input type="checkbox" id="agreement" v-model="registerData.agree">
                <label for="agreement">
                    <p>
                        Заполняя форму, я соглашаюсь с <b>обработкой персональных данных и политикой конфиденциальности</b>
                    </p>
                </label>
                <p
                    class="auth__checkbox-error"
                    v-if="registerErrors?.agree"
                >
                    {{ registerErrors.agree }}
                </p>
            </div>
            <button
                class="auth-btn"
                @click="sendRegisterForm"
            >
                Готово
            </button>
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">
    .auth {
        position: fixed;
        width: 100%;
        left: 50%;
        transform: translateX(-50%);
        bottom: 0;
        background: radial-gradient(83.46% 83.46% at 50% 50%, rgba(24, 23, 33, 0.2) 36.88%, rgba(108, 101, 137, 0.2) 100%), #1C1A26;
        border-radius: 25px 25px 0 0;
        padding: 19px 20px;

        &-title {
            font-style: normal;
            font-weight: 700;
            font-size: 25px;
            line-height: 34px;
            text-align: center;
            color: #FFFFFF;
            margin-bottom: 20px;
        }

        &__group {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
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

            &-error {
                margin-top: 4px;
                color: red;
                font-size: 12px;
            }
        }

        &__checkbox {
            margin-bottom: 20px;

            input {
                display: none;
            }

            label {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 7px;
                font-style: normal;
                font-weight: 400;
                font-size: 9px;
                line-height: 12px;
                color: #FFFFFF;

                &::before {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-shrink: 0;
                    content: ' ';
                    width: 16px;
                    height: 16px;
                    border: 2px solid #EFC30A;
                    color: #EFC30A;
                }
            }

            input:checked + label::before {
                content: '\2714';
            }

            &-error {
                margin-top: 4px;
                color: red;
                font-size: 12px;
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
