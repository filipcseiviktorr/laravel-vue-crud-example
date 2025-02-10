import { defineStore } from "pinia";
import axios from "axios";
import { CarBrand } from "../types/CarBrand";

export const useCarBrandStore = defineStore("carBrandStore", {
    state: () => ({
        brands: [] as CarBrand[],
    }),
    actions: {
        async index() {
            try {
                const { data } = await axios.get<{ data: CarBrand[] }>("/api/v1/car-brands");
                this.brands = data.data;
            } catch (error) {
                throw error;
            }
        },
        async store(name: string) {
            try {
                const { data } = await axios.post<{ data: CarBrand }>("/api/v1/car-brands", { name });
                return data.data;
            } catch (error) {
                throw error;
            }
        },
        async edit(id: number, name: string) {
            try {
                const { data } = await axios.put(`/api/v1/car-brands/${id}`, { name });
                return data.data;
            } catch (error) {
                throw error;
            }
        },
        async delete(id: number) {
            try {
                await axios.delete(`/api/v1/car-brands/${id}`);
            } catch (error) {
                throw error;
            }
        }
    }
});
