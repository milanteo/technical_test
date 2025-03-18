import { Component, computed, DestroyRef, inject, output } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import {MatListModule} from '@angular/material/list';
import { ApiOrder, OrdersService } from '../orders.service';
import { takeUntilDestroyed, toSignal } from '@angular/core/rxjs-interop';
import { DialogRef } from '@angular/cdk/dialog';
import { combineLatest, map, merge, tap } from 'rxjs';

@Component({
  selector: 'app-order-form',
  imports: [
    ReactiveFormsModule,
    MatFormFieldModule, 
    MatInputModule,
    MatButtonModule,
    MatListModule,
    MatIconModule
  ],
  templateUrl: './order-form.component.html',
  styleUrl: './order-form.component.css'
})
export class OrderFormComponent {

  dialogRef = inject(DialogRef);

  fb = inject(FormBuilder);

  orders = inject(OrdersService);

  onDestroy = inject(DestroyRef);

  createdOrder = output<ApiOrder>();

  form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    description: ['', Validators.required]
  });

  products = this.fb.nonNullable.array([
    this.fb.nonNullable.group({
      name: ['', Validators.required],
      price: [0, Validators.required]
    })
  ]);

  addProduct() {

    this.products.push(this.fb.nonNullable.group({
      name:  ['', Validators.required],
      price: [0, Validators.required]
    }));
  }

  remove(index: number) {

    this.products.removeAt(index);
  }

  onSubmit() {

    this.orders.createProduct({
      ...this.form.getRawValue(),
      products: this.products.getRawValue()
    }).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(order => {
      
      this.createdOrder.emit(order);

      this.dialogRef.close();
    });

  }
}
