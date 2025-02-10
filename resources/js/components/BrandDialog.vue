<template>
    <Dialog :header="isEditingBrand ? 'Edit Brand' : 'Add Brand'" v-model:visible="localShowBrandDialog"
            modal dismissableMask class="max-w-2xl p-4">
        <div class="p-fluid space-y-4">
            <div class="field pt-5">
                <label for="brandName" class="text-lg font-semibold">Brand Name</label>
                <InputText id="brandName" v-model="localBrandName" placeholder="Max 40 characters"
                           class="w-full p-2 rounded-md border focus:ring-blue-500"/>
            </div>
            <div class="flex justify-start pt-5">
                <Button label="Save" icon="pi pi-check" class="rounded-full bg-green-500 text-white p-3"
                        @click="isEditingBrand ? editBrand() : addBrand()"/>
            </div>
            <ul v-if="localErrors.length" class="text-red-500 mt-2 text-sm">
                <li>{{ localErrors }}</li>
            </ul>
        </div>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';

const props = defineProps({
    showBrandDialog: Boolean,
    isEditingBrand: Boolean,
    brandName: String,
    errors: Array,
    selectedBrand: Object
});

const emit = defineEmits(['update:showBrandDialog', 'update:isEditingBrand', 'update:brandName', 'update:errors', 'addBrand', 'editBrand']);

const localShowBrandDialog = ref(props.showBrandDialog);
const localIsEditingBrand = ref(props.isEditingBrand);
const localBrandName = ref(props.brandName);
const localErrors = ref(props.errors);

watch(() => props.showBrandDialog, (newVal) => {
    localShowBrandDialog.value = newVal;
});
watch(localShowBrandDialog, (newVal) => {
    emit('update:showBrandDialog', newVal);
});

watch(() => props.isEditingBrand, (newVal) => {
    localIsEditingBrand.value = newVal;
});
watch(localIsEditingBrand, (newVal) => {
    emit('update:isEditingBrand', newVal);
});

watch(() => props.brandName, (newVal) => {
    localBrandName.value = newVal;
});
watch(localBrandName, (newVal) => {
    emit('update:brandName', newVal);
});

watch(() => props.errors, (newVal) => {
    localErrors.value = newVal;
});
watch(localErrors, (newVal) => {
    emit('update:errors', newVal);
});

const addBrand = () => {
    emit('addBrand');
};

const editBrand = () => {
    emit('editBrand');
};
</script>
