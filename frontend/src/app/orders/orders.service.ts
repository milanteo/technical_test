import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { endpoint } from '../../variables';
import { NumberValueAccessor } from '@angular/forms';
import { Subject } from 'rxjs';

export interface CreateOrderDto { 
  name: string;
  description: string; 
  products: { 
    name: string;
    price: number; 
  }[];
};

export interface ApiOrder {
  id:          number
  name:        string;
  description: string;
  date:        string;
}

export interface ApiProduct {
  id:          number
  name:        string;
  price:       number;
}

@Injectable({
  providedIn: 'root'
})
export class OrdersService {

  http = inject(HttpClient);

  createdOrder$ = new Subject<ApiOrder>();

  updatedOrder$ = new Subject<ApiOrder>();

  fetchOrders() {

    return this.http.get<ApiOrder[]>(`${endpoint}/orders`, {});

  }

  fetchProducts(order: number) {

    return this.http.get<{
      id:    number
      name:  string;
      price: number;
    }[]>(`${endpoint}/orders/${order}/products`, {});
    
  }

  createProduct(data: CreateOrderDto) {

    return this.http.post<ApiOrder>(`${endpoint}/orders`, data);
  }

  deleteProduct(orderId: number, productId: number) {

    return this.http.delete<{}>(`${endpoint}/orders/${orderId}/products/${productId}`, {});
  }

  deleteOrder(orderId: number) {

    return this.http.delete<{}>(`${endpoint}/orders/${orderId}`, {});
  }

  patchOrder(orderId: number, data: { [k:string]: any }) {

    return this.http.patch<ApiOrder>(`${endpoint}/orders/${orderId}`, data);
  }

  patchProduct(orderId: number, productId: number, data: { [k:string]: any }) {

    return this.http.patch<ApiProduct>(`${endpoint}/orders/${orderId}/products/${productId}`, data);
  }

  addProduct(orderId: number, data: { [k:string]: any }) {

    return this.http.post<ApiProduct>(`${endpoint}/orders/${orderId}/products`, data);
  }

}
