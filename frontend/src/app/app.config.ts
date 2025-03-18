import { ApplicationConfig, inject, provideAppInitializer, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { AuthService } from './auth/auth.service';
import { catchError, Observable, of, switchMap, tap, throwError } from 'rxjs';
import { User } from './user.model';
import { HttpErrorResponse, HttpEvent, HttpHandlerFn, HttpRequest, provideHttpClient, withInterceptors } from '@angular/common/http';

const appInitializer = () => {    

  const auth = inject(AuthService);   

  return of(localStorage.getItem('access_token')).pipe(
    switchMap(token => !!token ? auth.profile().pipe(
      tap((user) => auth.currentUser$.next(new User(user))),
      catchError(() => of(null))
    ) : of(null))
  );

};

const JWTInterceptor = function(request: HttpRequest<unknown>, next: HttpHandlerFn): Observable<HttpEvent<unknown>> {

  const access_token = localStorage.getItem('access_token');

  if(access_token) {

    return next(request.clone({
      setHeaders: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`
      }
    }));

  }

  return next(request);

};

const refreshTokenInterceptor = function(request: HttpRequest<unknown>, next: HttpHandlerFn): Observable<HttpEvent<unknown>> {

  const auth = inject(AuthService);

  return next(request).pipe(
    catchError((exception: HttpErrorResponse) => {

      if(exception.error.detail == "Expired access token."){

        return auth.refreshToken().pipe(
          tap(({ accessToken }) => localStorage.setItem('access_token', accessToken)),
          switchMap(() => next(request.clone({
            setHeaders: {
              Authorization: `Bearer ${localStorage.getItem('access_token')}`
            }
          })))
        );

      }

      return throwError(() => exception);
    })
  );
  
};

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }), 
    provideRouter(routes),
    provideHttpClient(withInterceptors([
      JWTInterceptor,
      refreshTokenInterceptor
    ])),
    provideAppInitializer(appInitializer)
  ]
};
