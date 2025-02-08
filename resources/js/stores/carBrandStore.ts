import { defineStore } from "pinia";
import axios from "axios";
import { CarBrand } from "../types/CarBrand";

export const useCarBrandStore = defineStore("carBrandStore", {
    state: () => ({
        brands: [] as CarBrand[],
    }),
    actions: {
        async fetchCarBrands() {
            try {
                const { data } = await axios.get<{ data: CarBrand[] }>("/api/v1/car-brands");
                this.brands = data.data;
            } catch (error) {
                throw error;
            }
        },
        async addBrand(name: string) {
            try {
                const { data } = await axios.post<{ data: CarBrand }>("/api/v1/car-brands", { name });
                return data.data;
            } catch (error) {
                throw error;
            }
        },
        async editBrand(id: number, name: string) {
            try {
                const { data } = await axios.put(`/api/v1/car-brands/${id}`, { name });
                return data.data;
            } catch (error) {
                throw error;
            }
        },
        async deleteBrand(id: number) {
            try {
                await axios.delete(`/api/v1/car-brands/${id}`);
            } catch (error) {
                throw error;
            }
        }
    }
});
