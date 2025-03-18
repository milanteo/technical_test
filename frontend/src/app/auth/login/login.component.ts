import { Component, DestroyRef, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { AuthService } from '../auth.service';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import { switchMap, tap } from 'rxjs';
import { User } from '../../user.model';

@Component({
  selector: 'app-login',
  imports: [
    ReactiveFormsModule,
    MatFormFieldModule, 
    MatInputModule,
    MatButtonModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export default class LoginComponent {

  fb = new FormBuilder();

  onDestroy = inject(DestroyRef);

  auth = inject(AuthService);

  router = inject(Router);

  login = this.fb.nonNullable.group({
    email:    ['', Validators.required],
    password: ['', Validators.required]
  });

  onSubmit() {

    const { email, password } = this.login.getRawValue();

    this.auth.login({ email, password }).pipe(
      takeUntilDestroyed(this.onDestroy),
      tap(({ accessToken }) => localStorage.setItem('access_token', accessToken)),
      switchMap(() => this.auth.profile())
    ).subscribe((user) => {

      this.auth.currentUser$.next(new User(user));

      this.router.navigate(['orders']);

    });

  }

}
