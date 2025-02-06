import { defineStore } from "pinia";
import axios from "axios";
import { CarModel } from "../types/CarModel";

export const useCarModelStore = defineStore("carModelStore", {
    state: () => ({
        models: [] as CarModel[],
    }),
    actions: {
        async addModel(brandId: number, formData: FormData) {
            try {
                const response = await axios.post<{ data: CarModel }>(`/api/v1/car-models/${brandId}`, formData);
                return response.data.data;
            } catch (error) {
                throw error;
            }
        },
        async editModel(id: number, formData: FormData) {
            try {
                const response = await axios.post(`/api/v1/car-models/${id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                return response.data.data;
            } catch (error) {
                throw error;
            }
        },
        async deleteModel(id: number) {
            try {
                await axios.delete(`/api/v1/car-models/${id}`);
            } catch (error) {
                throw error;
            }
        }
    }
});
