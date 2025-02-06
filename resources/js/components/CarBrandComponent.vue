<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useCarBrandStore } from '../stores/carBrandStore.ts';
import { useCarModelStore } from '../stores/carModelStore.ts';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import FileUpload from 'primevue/fileupload';

const carBrandStore = useCarBrandStore();
const carModelStore = useCarModelStore();

const state = ref({
    selectedBrand: null,
    selectedModel: null,
    brandName: '',
    modelName: '',
    errors: [],
    showBrandDialog: false,
    showModelDialog: false,
    isEditingBrand: false,
    isEditingModel: false,
    itemsPerPage: 5,
    page: 1,
});

const filteredCarList = computed(() =>
    state.value.selectedBrand?.car_models?.map(model => ({
        id: model.id,
        model: model.name,
        path: model.path
    })) || []
);

const paginatedCarList = computed(() => {
    const start = (state.value.page - 1) * state.value.itemsPerPage;
    const end = start + state.value.itemsPerPage;
    return filteredCarList.value.slice(start, end);
});

const pageCount = computed(() => Math.ceil(filteredCarList.value.length / state.value.itemsPerPage));

onMounted(() => {
    carBrandStore.fetchCarBrands();
});

watch(() => state.value.selectedBrand, () => {
    state.value.page = 1;
});

const resetFields = () => {
    state.value.brandName = '';
    state.value.modelName = '';
    state.value.selectedModel = null;
    state.value.errors = [];
};

const handleError = (error) => {
    state.value.errors = error.response?.data?.message || ['An error occurred'];
};

const addBrand = async () => {
    try {
        const newBrand = await carBrandStore.addBrand(state.value.brandName);
        carBrandStore.brands.push(newBrand);
        state.value.selectedBrand = newBrand;
        resetFields();
        state.value.showBrandDialog = false;
    } catch (error) {
        handleError(error);
    }
};

const editBrand = async () => {
    try {
        const updatedBrand = await carBrandStore.editBrand(state.value.selectedBrand.id, state.value.brandName);
        const index = carBrandStore.brands.findIndex(brand => brand.id === updatedBrand.id);
        if (index !== -1) {
            carBrandStore.brands[index] = updatedBrand;
        }
        state.value.selectedBrand = updatedBrand;
        resetFields();
        state.value.showBrandDialog = false;
    } catch (error) {
        handleError(error);
    }
};

const deleteBrand = async () => {
    try {
        await carBrandStore.deleteBrand(state.value.selectedBrand.id);
        state.value.selectedBrand = null;
        await carBrandStore.fetchCarBrands();
    } catch (error) {
        handleError(error);
    }
};

const addModel = async () => {
    try {
        const formData = new FormData();
        formData.append('name', state.value.modelName);
        if (state.value.newModelImage instanceof File) {
            formData.append('image', state.value.newModelImage);
        }

        const response = await carModelStore.addModel(state.value.selectedBrand.id, formData);

        state.value.selectedBrand.car_models = state.value.selectedBrand.car_models || [];
        state.value.selectedBrand.car_models.push(response);
        resetFields();
        state.value.showModelDialog = false;
        await carBrandStore.fetchCarBrands();
    } catch (error) {
        handleError(error);
    }
};

const editModel = async () => {
    try {
        const formData = new FormData();
        formData.append('name', state.value.modelName);
        if (state.value.newModelImage instanceof File) {
            formData.append('image', state.value.newModelImage);
        }
        formData.append('_method', 'PUT');

        const response = await carModelStore.editModel(state.value.selectedModel.id, formData);
        const index = state.value.selectedBrand.car_models.findIndex(model => model.id === state.value.selectedModel.id);
        if (index !== -1) {
            state.value.selectedBrand.car_models[index] = response;
        }
        resetFields();
        state.value.showModelDialog = false;
        await carBrandStore.fetchCarBrands();
    } catch (error) {
        handleError(error);
    }
};

const deleteCar = async (car) => {
    try {
        await carModelStore.deleteModel(car.id);
        state.value.selectedBrand.car_models = state.value.selectedBrand.car_models.filter(model => model.id !== car.id);
    } catch (error) {
        handleError(error);
    }
};

const openAddBrandDialog = () => {
    resetFields();
    state.value.isEditingBrand = false;
    state.value.showBrandDialog = true;
};

const openEditBrandDialog = () => {
    state.value.brandName = state.value.selectedBrand.name;
    state.value.isEditingBrand = true;
    state.value.showBrandDialog = true;
};

const openAddModelDialog = () => {
    resetFields();
    state.value.isEditingModel = false;
    state.value.showModelDialog = true;
};

const editCar = (car) => {
    state.value.modelName = car.model;
    state.value.selectedModel = car;
    state.value.isEditingModel = true;
    state.value.showModelDialog = true;
};

const onFileSelect = (event) => {
    if (event.files.length > 0) {
        state.value.newModelImage = event.files[0];
    }
};
</script>

<template>
    <div class="flex flex-col items-center min-h-screen p-6 mx-auto">
        <div class="w-full max-w-4xl mb-8 mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div class="w-full">
                <Dropdown v-model="state.selectedBrand" :options="carBrandStore.brands" optionLabel="name"
                          placeholder="Select a Brand"
                          class="w-full p-dropdown border rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex justify-end space-x-4">
                <Button icon="pi pi-plus" class="p-button-rounded p-button-success shadow-lg"
                        @click="openAddBrandDialog"/>
                <Button v-if="state.selectedBrand" icon="pi pi-pencil" class="p-button-rounded p-button-info shadow-lg"
                        @click="openEditBrandDialog"/>
                <Button v-if="state.selectedBrand" icon="pi pi-trash" class="p-button-rounded p-button-danger shadow-lg"
                        @click="deleteBrand"/>
            </div>
        </div>

        <div class="w-full max-w-6xl">
            <v-data-table
                :items="filteredCarList"
                :items-per-page="state.itemsPerPage"
            >
                <template v-slot:headers>
                    <tr>
                        <th class="px-6 py-3 bg-blue-600 text-white text-center rounded-tl-lg w-1/3">Model</th>
                        <th class="px-6 py-3 bg-blue-600 text-white text-center w-1/3">Image</th>
                        <th class="px-6 py-3 bg-blue-600 text-white text-center rounded-tr-lg w-1/3">
                            Actions<span>
                                <Button v-if="state.selectedBrand" icon="pi pi-plus"
                                        class="p-button-rounded p-button-success ml-2" @click="openAddModelDialog"/>
                        </span>
                        </th>
                    </tr>
                </template>
                <template v-slot:top>
                    <div class="text-center pt-2">
                        <v-pagination v-model="state.page" :length="pageCount"></v-pagination>
                    </div>
                </template>
                <template v-slot:body>
                    <tr v-for="car in paginatedCarList" :key="car.id">
                        <td class="px-6 py-3 text-center w-1/3">{{ car.model }}</td>
                        <td class="px-6 py-3 text-center w-1/3">
                            <img :src="car.path" class="mx-auto rounded"/>
                        </td>
                        <td class="px-6 py-3 text-center w-1/3">
                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-info mr-2"
                                    @click="editCar(car)"/>
                            <Button icon="pi pi-trash" class="p-button-rounded p-button-danger"
                                    @click="deleteCar(car)"/>
                        </td>
                    </tr>
                </template>
                <template v-slot:bottom/>
            </v-data-table>
        </div>

        <Dialog :header="state.isEditingBrand ? 'Edit Brand' : 'Add Brand'" v-model:visible="state.showBrandDialog"
                modal dismissableMask class="rounded-xl shadow-xl">
            <div class="p-fluid">
                <div class="field">
                    <label for="brandName" class="text-lg font-semibold">Brand Name</label>
                    <InputText id="brandName" v-model="state.brandName" placeholder="Max 40 characters"
                               class="w-full p-inputtext-lg rounded-md border focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div class="mt-6 flex justify-start">
                    <Button label="Save" icon="pi pi-check" class="p-button-rounded p-button-primary w-32"
                            @click="state.isEditingBrand ? editBrand() : addBrand()"/>
                </div>
                <ul v-if="state.errors.length" class="text-red-500 mt-2 text-sm">
                    <li>{{ state.errors }}</li>
                </ul>
            </div>
        </Dialog>

        <Dialog :header="state.isEditingModel ? 'Edit Model' : 'Add Model'" v-model:visible="state.showModelDialog"
                modal dismissableMask class="rounded-xl shadow-xl">
            <div class="p-fluid">
                <div class="field">
                    <label for="modelName" class="text-lg font-semibold">Model Name</label>
                    <InputText id="modelName" v-model="state.modelName" placeholder="Max 40 characters"
                               class="w-full p-inputtext-lg rounded-md border focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div class="pt-5">
                    <label for="modelImage" class="text-lg font-semibold">Model Image</label>
                    <FileUpload
                        :maxFileSize="2000000"
                        mode="basic"
                        name="image"
                        accept="image/*"
                        @select="onFileSelect"
                        customUpload
                        class="rounded-md shadow-md"
                    />
                </div>
                <div class="mt-6 flex justify-start">
                    <Button label="Save" icon="pi pi-check" class="p-button-rounded p-button-primary w-32"
                            @click="state.isEditingModel ? editModel() : addModel()"/>
                </div>
                <ul v-if="state.errors.length" class="text-red-500 mt-2 text-sm">
                    <li>{{ state.errors }}</li>
                </ul>
            </div>
        </Dialog>
    </div>
</template>

<style>
.p-button-icon-left.pi.pi-plus::before {
    content: "\e930";
}
</style>
