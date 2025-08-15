<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
import ProgressBar from "@/Components/ProgressBar.vue";


</script>

<template>
    <Head title="Постскриптум" />
    <MainLayout>
        <ProgressBar/>

        <div class="info">
            <h1 class="info-title">Постскриптум</h1>
            <div class="info-block">
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
        </div>
    </MainLayout>
</template>

<style scoped lang="scss">

</style>
