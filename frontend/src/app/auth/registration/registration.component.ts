import { Component, DestroyRef, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { AuthService } from '../auth.service';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import { samePassword } from './validators';

@Component({
  selector: 'app-registration',
  imports: [
    ReactiveFormsModule,
    MatFormFieldModule, 
    MatInputModule,
    MatButtonModule
  ],
  templateUrl: './registration.component.html',
  styleUrl: './registration.component.css'
})
export default class RegistrationComponent {

  fb = new FormBuilder();

  auth = inject(AuthService);

  onDestroy = inject(DestroyRef);

  router = inject(Router);

  registration = this.fb.nonNullable.group({
    email:        ['', Validators.required],
    password:     ['', Validators.required],
    password_rep: ['', Validators.required]
  }, { validators: [ samePassword ] });

  onSubmit() {

    const { email, password } = this.registration.getRawValue();

    this.auth.registration({ email, password }).pipe(
      takeUntilDestroyed(this.onDestroy)
    ).subscribe(() => this.router.navigate(['auth', 'login']));

  }

}
