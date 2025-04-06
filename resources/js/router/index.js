import { createRouter, createWebHistory } from 'vue-router'

import home from '../components/CarBrandComponent.vue'

const routes = [
    {
        path: '/',
        component: home,
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
