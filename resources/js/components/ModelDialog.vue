<template>
    <Dialog
        :header="isEditingModel ? 'Edit Model' : 'Add Model'"
        v-model:visible="localShowModelDialog"
        modal
        dismissableMask
        class="rounded-xl shadow-xl p-6"
    >
        <div class="p-fluid space-y-6">
            <div class="field">
                <label for="modelName" class="text-lg font-semibold">Model Name</label>
                <InputText
                    id="modelName"
                    v-model="localModelName"
                    placeholder="Max 40 characters"
                    class="w-full p-2 rounded-md border focus:ring-blue-500"
                />
            </div>
            <div class="pt-5">
                <label for="modelImage" class="text-lg font-semibold">Model Image</label>
                <FilePond
                    :allowMultiple="false"
                    :maxFileSize="2000000"
                    :acceptedFileTypes="['image/*']"
                    ref="pond"
                    class=""
                    :files="
                        selectedModel?.path
                            ? [{ source: selectedModel.path, options: { type: 'remote' } }]
                            : []
                    "
                    @updatefiles="onFileChange"
                />
            </div>
            <div class="mt-6 flex justify-start">
                <Button
                    label="Save"
                    icon="pi pi-check"
                    class="rounded-full bg-green-500 text-white p-2"
                    @click="isEditingModel ? editModel() : addModel()"
                />
            </div>
            <ul v-if="localErrors.length" class="text-red-500 mt-2 text-sm">
                <li>{{ localErrors }}</li>
            </ul>
        </div>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview)

const props = defineProps({
    showModelDialog: Boolean,
    isEditingModel: Boolean,
    modelName: String,
    errors: Array,
    selectedModel: Object,
})

const emit = defineEmits([
    'update:showModelDialog',
    'update:isEditingModel',
    'update:modelName',
    'update:errors',
    'addModel',
    'editModel',
    'onFileChange',
])

const localShowModelDialog = ref(props.showModelDialog)
const localIsEditingModel = ref(props.isEditingModel)
const localModelName = ref(props.modelName)
const localErrors = ref(props.errors)

watch(
    () => props.showModelDialog,
    (newVal) => {
        localShowModelDialog.value = newVal
    },
)
watch(localShowModelDialog, (newVal) => {
    emit('update:showModelDialog', newVal)
})

watch(
    () => props.isEditingModel,
    (newVal) => {
        localIsEditingModel.value = newVal
    },
)
watch(localIsEditingModel, (newVal) => {
    emit('update:isEditingModel', newVal)
})

watch(
    () => props.modelName,
    (newVal) => {
        localModelName.value = newVal
    },
)
watch(localModelName, (newVal) => {
    emit('update:modelName', newVal)
})

watch(
    () => props.errors,
    (newVal) => {
        localErrors.value = newVal
    },
)
watch(localErrors, (newVal) => {
    emit('update:errors', newVal)
})

const addModel = () => {
    emit('addModel')
}

const editModel = () => {
    emit('editModel')
}

const onFileChange = (fileItems) => {
    emit('onFileChange', fileItems)
}
</script>
