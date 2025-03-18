import { Component, DestroyRef, inject, output } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { ApiOrder, ApiProduct, OrdersService } from '../../orders.service';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { DialogRef } from '@angular/cdk/dialog';

@Component({
  selector: 'app-add-product',
  imports: [
    ReactiveFormsModule,
    MatFormFieldModule, 
    MatInputModule,
    MatButtonModule
  ],
  templateUrl: './add-product.component.html',
  styleUrl: './add-product.component.css'
})
export class AddProductComponent {

  data: ApiOrder = inject(MAT_DIALOG_DATA);

  dialogRef = inject(DialogRef);

  fb = inject(FormBuilder);

  orders = inject(OrdersService);

  onDestroy = inject(DestroyRef);

  created = output<ApiProduct>();

  form = this.fb.nonNullable.group({
    name: ['', Validators.required],
    price: [0, Validators.required]
  });

  onSubmit() {

    this.orders.addProduct(this.data.id, this.form.getRawValue()).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(p => {

      this.created.emit(p);
      
      this.dialogRef.close();
    })

  }

}
