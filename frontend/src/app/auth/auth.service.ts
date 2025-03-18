import { inject, Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { User } from '../user.model';
import { HttpClient } from '@angular/common/http';
import { endpoint } from '../../variables';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  router = inject(Router);

  http = inject(HttpClient);

  currentUser$ = new BehaviorSubject<User | null>(null);

  logout() {

    this.router.navigate(['auth', 'login']);
    
    this.currentUser$.next(null);

    localStorage.removeItem('access_token');

  }

  profile() {

    return this.http.get<{ id:number; email: string }>(`${endpoint}/profile`);
  }

  refreshToken() {

    return this.http.post<{ accessToken: string }>(`${endpoint}/auth/refresh-token`, {});
  }

  registration(data: { email: string, password: string }) {

    return this.http.post<{ id:number; email: string }>(`${endpoint}/auth/registration`, data);
  }

  login(data: { email: string, password: string }) {

    return this.http.post<{ accessToken: string }>(`${endpoint}/auth/login`, data);
  }

}
