import { Component, DestroyRef, inject, OnInit, output } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { ApiOrder, OrdersService } from '../../orders.service';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { DialogRef } from '@angular/cdk/dialog';

@Component({
  selector: 'app-edit-order',
  imports: [
    ReactiveFormsModule,
    MatFormFieldModule, 
    MatInputModule,
    MatButtonModule
  ],
  templateUrl: './edit-order.component.html',
  styleUrl: './edit-order.component.css'
})
export class EditOrderComponent implements OnInit {

  data: ApiOrder = inject(MAT_DIALOG_DATA);

  dialogRef = inject(DialogRef);

  fb = inject(FormBuilder);

  orders = inject(OrdersService);

  onDestroy = inject(DestroyRef);

  updated = output<ApiOrder>();

  form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    description: ['', Validators.required]
  });

  ngOnInit(): void {
    
    this.form.patchValue(this.data);

  }

  onSubmit() {

    this.orders.patchOrder(this.data.id, this.form.getRawValue()).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(order => {

      this.updated.emit(order);

      this.dialogRef.close();
    });

  }

}
