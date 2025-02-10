<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useCarBrandStore } from '../stores/carBrandStore.ts';
import { useCarModelStore } from '../stores/carModelStore.ts';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import Image from 'primevue/image';

import BrandDialog from "../components/BrandDialog.vue";
import ModelDialog from "../components/ModelDialog.vue";

const carBrandStore = useCarBrandStore();
const carModelStore = useCarModelStore();

const selectedBrand = ref(null);
const selectedModel = ref(null);
const brandName = ref('');
const modelName = ref('');
const errors = ref([]);
const showBrandDialog = ref(false);
const showModelDialog = ref(false);
const isEditingBrand = ref(false);
const isEditingModel = ref(false);
const itemsPerPage = ref(5);
const page = ref(1);
const image = ref(null);

const filteredCarList = computed(() =>
    selectedBrand.value?.car_models?.map(model => ({
        id: model.id,
        model: model.name,
        path: model.path,
        thumbnail_path: model.thumbnail_path
    })) || []
);

const paginatedCarList = computed(() => {
    const start = (page.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredCarList.value.slice(start, end);
});

const pageCount = computed(() => Math.ceil(filteredCarList.value.length / itemsPerPage.value));

onMounted(() => {
    carBrandStore.index();
});

const resetFields = () => {
    brandName.value = '';
    modelName.value = '';
    selectedModel.value = null;
    errors.value = [];
};

const handleError = (error) => {
    errors.value = error.response?.data?.message || ['An error occurred'];
};

const addBrand = async () => {
    try {
        const newBrand = await carBrandStore.store(brandName.value);
        carBrandStore.brands.push(newBrand);
        selectedBrand.value = newBrand;
        showBrandDialog.value = false;
    } catch (error) {
        handleError(error);
    }
};

const editBrand = async () => {
    try {
        const updatedBrand = await carBrandStore.edit(selectedBrand.value.id, brandName.value);
        const index = carBrandStore.brands.findIndex(brand => brand.id === updatedBrand.id);
        if (index !== -1) {
            carBrandStore.brands[index] = updatedBrand;
        }
        selectedBrand.value = updatedBrand;
        resetFields();
        showBrandDialog.value = false;
    } catch (error) {
        handleError(error);
    }
};

const deleteBrand = async () => {
    try {
        await carBrandStore.delete(selectedBrand.value.id);
        carBrandStore.brands = carBrandStore.brands.filter(
            (brand) => brand.id !== selectedBrand.value.id
        );
    } catch (error) {
        handleError(error);
    }
};

const saveModel = async () => {
    try {
        const formData = new FormData();
        formData.append('name', modelName.value);
        formData.append('image', image.value);

        let response;
        if (isEditingModel.value) {
            formData.append('_method', 'PUT');
            response = await carModelStore.edit(selectedModel.value.id, formData);
            const index = selectedBrand.value.car_models.findIndex(model => model.id === selectedModel.value.id);
            if (index !== -1) {
                selectedBrand.value.car_models[index] = response;
            }
        } else {
            response = await carModelStore.store(selectedBrand.value.id, formData);
            selectedBrand.value.car_models = selectedBrand.value.car_models || [];
            selectedBrand.value.car_models.unshift(response);
        }

        resetFields();
        showModelDialog.value = false;
        await carBrandStore.index();
    } catch (error) {
        handleError(error);
    }
};

const deleteCar = async (car) => {
    try {
        await carModelStore.delete(car.id);
        selectedBrand.value.car_models = selectedBrand.value.car_models.filter(model => model.id !== car.id);
    } catch (error) {
        handleError(error);
    }
};

const openAddBrandDialog = () => {
    resetFields();
    isEditingBrand.value = false;
    showBrandDialog.value = true;
};

const openEditBrandDialog = () => {
    resetFields();
    brandName.value = selectedBrand.value.name;
    isEditingBrand.value = true;
    showBrandDialog.value = true;
};

const openAddModelDialog = () => {
    resetFields();
    isEditingModel.value = false;
    showModelDialog.value = true;
    image.value = null;
};

const editCar = (car) => {
    modelName.value = car.model;
    selectedModel.value = car;
    isEditingModel.value = true;
    showModelDialog.value = true;
    image.value = car.path ? {source: car.path, options: {type: 'remote'}} : null;
};

const onFileChange = (fileItems) => {
    image.value = fileItems.length === 1 ? fileItems[0].file : null;
};

</script>

<template>
    <div class="flex flex-col items-center min-h-screen p-6 bg-gray-100">
        <div class="w-full max-w-4xl mb-8 mt-4 grid grid-cols-1 md:grid-cols-2 items-center">
            <div class="w-full">
                <Dropdown v-model="selectedBrand" :options="carBrandStore.brands" optionLabel="name"
                          placeholder="Select a Brand"
                          class="w-full border rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 p-2 mt-2"/>
            </div>
            <div class="flex justify-center space-x-2 mt-4">
                <Button icon="pi pi-plus" class="rounded-full bg-green-500 shadow-lg text-white p-2"
                        @click="openAddBrandDialog"/>
                <Button v-if="selectedBrand" icon="pi pi-pencil"
                        class="rounded-full bg-blue-500 shadow-lg text-white p-2" @click="openEditBrandDialog"/>
                <Button v-if="selectedBrand" icon="pi pi-trash"
                        class="rounded-full bg-red-500 shadow-lg text-white p-2" @click="deleteBrand"/>
            </div>
        </div>
        <div class="w-full">
            <v-data-table v-if="selectedBrand"
                          :items="filteredCarList"
                          :items-per-page="itemsPerPage"
            >
                <template v-slot:top>
                    <div class="bg-gray-100">
                        <div v-if="paginatedCarList.length" class="text-center pt-2 text-black">
                            <v-pagination v-model="page" :length="pageCount"></v-pagination>
                        </div>
                        <div class="flex justify-center items-center mt-4 space-x-2">
                            <span class="text-xl font-semibold">Car Models</span>
                            <Button v-if="selectedBrand" icon="pi pi-plus"
                                    class="rounded-full bg-green-500 shadow-lg text-white p-2"
                                    @click="openAddModelDialog"/>
                        </div>
                    </div>
                </template>
                <template v-slot:headers v-if="paginatedCarList.length">
                    <th class="bg-blue-600 text-white px-6 py-3 rounded-tl-lg"><span>Model</span></th>
                    <th class="px-6 py-3 bg-blue-600 text-white text-center w-1/3"><span>Image</span></th>
                    <th class="px-6 py-3 bg-blue-600 text-white text-center rounded-tr-lg w-1/3"><span>Actions</span></th>
                </template>
                <template v-slot:body>
                    <tr v-for="(car, index) in paginatedCarList" :key="car.id"
                        :class="{'bg-gray-200': index % 2 === 0, 'bg-white': index % 2 !== 0}">
                        <td class="px-6 py-3 text-center w-1/3">{{ car.model }}</td>
                        <td class="px-6 py-3 text-center w-1/3">
                            <Image :src="car.path" preview class="hover:opacity-75 rounded-lg w-full h-auto">
                                <template #previewicon>
                                    <i class="pi pi-search text-white"></i>
                                </template>
                                <template #image>
                                    <img :src="car.thumbnail_path" class="w-full md:max-w-[300px] h-auto mx-auto"/>
                                </template>
                            </Image>
                        </td>
                        <td class="px-6 py-3 text-center w-1/3">
                            <Button icon="pi pi-pencil" class="rounded-full bg-blue-500 shadow-lg text-white p-2 mr-2"
                                    @click="editCar(car)"/>
                            <Button icon="pi pi-trash" class="rounded-full bg-red-500 shadow-lg text-white p-2"
                                    @click="deleteCar(car)"/>
                        </td>
                    </tr>
                </template>
                <template v-slot:bottom/>
            </v-data-table>
        </div>

        <BrandDialog
            :showBrandDialog="showBrandDialog"
            :isEditingBrand="isEditingBrand"
            :brandName="brandName"
            :errors="errors"
            :selectedBrand="selectedBrand"
            @update:showBrandDialog="showBrandDialog = $event"
            @update:isEditingBrand="isEditingBrand = $event"
            @update:brandName="brandName = $event"
            @update:errors="errors = $event"
            @addBrand="addBrand"
            @editBrand="editBrand"
        />

        <ModelDialog
            :showModelDialog="showModelDialog"
            :isEditingModel="isEditingModel"
            :modelName="modelName"
            :errors="errors"
            :selectedModel="selectedModel"
            @update:showModelDialog="showModelDialog = $event"
            @update:isEditingModel="isEditingModel = $event"
            @update:modelName="modelName = $event"
            @update:errors="errors = $event"
            @addModel="saveModel"
            @editModel="saveModel"
            @onFileChange="onFileChange"
        />
    </div>
</template>
