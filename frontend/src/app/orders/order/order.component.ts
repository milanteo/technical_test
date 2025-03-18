import { Component, DestroyRef, inject, input, OnInit, output, signal } from '@angular/core';
import { takeUntilDestroyed, toSignal } from '@angular/core/rxjs-interop';
import {MatExpansionModule} from '@angular/material/expansion';
import { ApiOrder, ApiProduct, OrdersService } from '../orders.service';
import {MatListModule} from '@angular/material/list';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { OrderFormComponent } from '../order-form/order-form.component';
import { FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { EditOrderComponent } from './edit-order/edit-order.component';
import { AddProductComponent } from './add-product/add-product.component';
import { single, Subject, switchMap } from 'rxjs';
import { setThrowInvalidWriteToSignalError } from '@angular/core/primitives/signals';

@Component({
  selector: 'app-order',
  imports: [MatExpansionModule, MatListModule, MatButtonModule, MatIconModule, MatInputModule, ReactiveFormsModule],
  templateUrl: './order.component.html',
  styleUrl: './order.component.css'
})
export class OrderComponent implements OnInit {

  orders = inject(OrdersService);

  fb = inject(FormBuilder);

  onDestroy = inject(DestroyRef);

  order = input.required<ApiOrder>();

  deletedOrder = output<ApiOrder>();

  dialog = inject(MatDialog);

  expanded = signal(false);

  updated = output<ApiOrder>();

  products = signal<(ApiProduct & { editing: FormGroup<{
    name: FormControl<string>,
    price: FormControl<number>
  }> | null })[]>([]);

  fetchProducts$ = new Subject<void>();

  onOpen() {

    this.fetchProducts$.next();

  }

  ngOnInit(): void {

    this.fetchProducts$.pipe(
      takeUntilDestroyed(this.onDestroy),
      switchMap(() => this.orders.fetchProducts(this.order().id))
    ).subscribe(products => this.products.set(products.map(p => ({ ...p, editing: null }))));

  }

  deleteProduct(productId: number) {

    this.orders.deleteProduct(this.order().id, productId).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(() => this.products.update(p => p.filter(e => e.id != productId)));
  }

  deleteOrder(event: MouseEvent, order: ApiOrder) {

    event.stopPropagation();

    this.orders.deleteOrder(order.id).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(() => this.deletedOrder.emit(this.order()));
  }

  editOrder(event: MouseEvent, order: ApiOrder) {

    event.stopPropagation();

    const dialogRef = this.dialog.open(EditOrderComponent, {
      data: { ...this.order() }
    });

    dialogRef.componentInstance.updated.subscribe(o => this.updated.emit(o));
    
  }

  editProduct(product: ApiProduct & { editing: FormGroup<{
    name: FormControl<string>,
    price: FormControl<number>
  }> | null }, enabled: boolean) {

    this.products.update(p => {

      const index = p.findIndex(el => el.id == product.id);

      p.splice(index, 1, {
        ...product,
        editing: enabled ? this.fb.nonNullable.group({
          name:  [product.name, Validators.required],
          price: [product.price, Validators.required]
        }) : null
      });

      return p;

    });
    
  }

  saveProduct(productId: number, form: FormGroup<{
    name: FormControl<string>,
    price: FormControl<number>
  }>) {

    this.orders.patchProduct(this.order().id, productId, form.getRawValue()).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(product => this.products.update(p => {

      const index = p.findIndex(e => e.id == productId);

      p.splice(index, 1, { ...product, editing: null });

      return p;

    }));

  }

  addProduct(event: MouseEvent, order: ApiOrder) {

    event.stopPropagation();

    const dialogref = this.dialog.open(AddProductComponent, {
      data: this.order()
    });

    dialogref.componentInstance.created.subscribe(p => this.products.update(ps => {

      ps.push({ ...p, editing: null });

      return ps;
    }));

  }

}
