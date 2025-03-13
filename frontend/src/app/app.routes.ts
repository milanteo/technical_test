import { inject } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivateFn, Router, RouterStateSnapshot, Routes } from '@angular/router';
import { AuthService } from './auth/auth.service';
import { map, tap } from 'rxjs';

const JWTguard: CanActivateFn = (
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
) => {

    const auth = inject(AuthService);

    const router = inject(Router);

    return auth.currentUser$.pipe(
        map(user => !!user ? true : router.parseUrl('/auth/login'))
    );
};

export const routes: Routes = [
    { path: 'auth',   loadChildren:  () => import('./auth/auth.routes').then(h => h.routes) },
    { path: 'orders', loadChildren:  () => import('./orders/orders.routes').then(h => h.routes), canActivate: [JWTguard] },
    { path: '**',     loadComponent: () => import('./not-found/not-found.component') }
];
