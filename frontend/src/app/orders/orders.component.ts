import { Component, DestroyRef, inject, OnInit, signal } from '@angular/core';
import {MatSidenavModule} from '@angular/material/sidenav';
import { AuthService } from '../auth/auth.service';
import { takeUntilDestroyed, toSignal } from '@angular/core/rxjs-interop';
import {MatExpansionModule} from '@angular/material/expansion';
import { ApiOrder, OrdersService } from './orders.service';
import {MatButtonModule} from '@angular/material/button';
import {MatIconModule} from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { OrderFormComponent } from './order-form/order-form.component';
import { Subject, switchMap } from 'rxjs';
import { OrderComponent } from './order/order.component';
import {BreakpointObserver} from '@angular/cdk/layout';

@Component({
  selector: 'app-orders',
  imports: [MatSidenavModule, MatExpansionModule, MatButtonModule, MatIconModule, OrderComponent],
  templateUrl: './orders.component.html',
  styleUrl: './orders.component.css'
})
export default class OrdersComponent implements OnInit {

  auth = inject(AuthService);

  onDestroy = inject(DestroyRef);

  breakpoint = inject(BreakpointObserver);

  orders = inject(OrdersService);

  currentUser = toSignal(this.auth.currentUser$);

  ordersList = signal<ApiOrder[]>([]);

  fetchOrders$ = new Subject<void>();

  dialog = inject(MatDialog);

  openDialog(): void {

    const dialogRef = this.dialog.open(OrderFormComponent);

    dialogRef.componentInstance.createdOrder.subscribe(() => this.fetchOrders$.next());

  }

  ngOnInit(): void {
    
    this.fetchOrders$.pipe(
      takeUntilDestroyed(this.onDestroy),
      switchMap(() => this.orders.fetchOrders())
    ).subscribe(orders => this.ordersList.set(orders));

    this.orders.createdOrder$.pipe(takeUntilDestroyed(this.onDestroy)).subscribe(() => this.fetchOrders$.next());

    this.fetchOrders$.next();

  }

  logout() {
    
    this.auth.logout();
  }

  deletedOrder(order: ApiOrder) {

    this.ordersList.update(o => o.filter(e => e.id != order.id));
  }

  updatedOrder(order: ApiOrder) {

    this.ordersList.update(o => {

      const index = o.findIndex(e => e.id == order.id);

      o.splice(index, 1, { ...order });

      return o;

    });
  }

}
