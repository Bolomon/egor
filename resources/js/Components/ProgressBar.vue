<script setup>
import {computed, onBeforeMount, ref} from "vue";

const userToken = localStorage.getItem("auth-token")

const steps = ref([])

const getCountCompletedUserSteps = () => {
    const config = {
        headers: {
            'Authorization': 'Bearer ' + userToken
        }
    }

    axios
        .get('/api/quests ', config)
        .then((data) => {
            steps.value = data.data
        })
}

onBeforeMount(() => {
    getCountCompletedUserSteps()
})
</script>

<template>
    <div
        class="progress"
    >
        <div class="progress-item" v-for="(step, index) in steps" :key="index">
            <img
                src="/img/convert_open.svg"
                alt="completed"
                v-if="step.completed"
            >
            <img
                src="/img/convert_closed.svg"
                alt="closed"
                v-else
            >
            <span
                :class="{active: step.completed}"
            ></span>
        </div>
    </div>
</template>

<style scoped lang="scss">
.progress {
    position: relative;
    display: grid;
    align-items: center;
    grid-template-columns: repeat(12, 1fr);

    &::before {
        content: "";
        display: block;
        left: 0;
        bottom: 2px;
        width: 100%;
        max-width: 393px;
        height: 10px;
        background-color: #313039;
        border-radius: 150px;
        position: absolute;
    }

    &-item {
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 5px;

        span {
            display: block;
            background-color: #313039;
            width: 14px;
            height: 14px;
            border-radius: 50%;

            &.active {
                background-color: #EFC30A;
            }
        }
    }
}
</style>
