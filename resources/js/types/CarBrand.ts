import {CarModel} from "./CarModel";

export interface CarBrand {
    id: number;
    name: string;
    car_models: CarModel[] | null;
}
