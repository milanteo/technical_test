import { Component } from '@angular/core';
import { FormBuilder, ReactiveFormsModule } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';

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

  login = this.fb.nonNullable.group({
    email:        '',
    password:     '',
    password_rep: ''
  });

  onSubmit() {

  }

}
