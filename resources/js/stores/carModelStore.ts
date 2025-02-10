import { defineStore } from "pinia";
import axios from "axios";
import { CarModel } from "../types/CarModel";

export const useCarModelStore = defineStore("carModelStore", {
    state: () => ({
        models: [] as CarModel[],
    }),
    actions: {
        async store(brandId: number, formData: FormData) {
            try {
                const { data } = await axios.post<{ data: CarModel }>(`/api/v1/car-models/${brandId}`, formData);
                return data.data;
            } catch (error) {
                throw error;
            }
        },
        async edit(id: number, formData: FormData) {
            try {
                const { data } = await axios.post(`/api/v1/car-models/${id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                return data.data;
            } catch (error) {
                throw error;
            }
        },
        async delete(id: number) {
            try {
                await axios.delete(`/api/v1/car-models/${id}`);
            } catch (error) {
                throw error;
            }
        }
    }
});
