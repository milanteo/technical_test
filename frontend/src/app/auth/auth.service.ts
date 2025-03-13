import { inject, Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { User } from '../user.model';
import { HttpClient } from '@angular/common/http';
import { endpoint } from '../../variables';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  http = inject(HttpClient);

  currentUser$ = new BehaviorSubject<User | null>(null);

  profile() {

    return this.http.get<{ id:number; email: string }>(`${endpoint}/profile`);
  }

  refreshToken() {

    return this.http.post<{ accessToken: string }>(`${endpoint}/auth/refresh-token`, {});
  }

}
