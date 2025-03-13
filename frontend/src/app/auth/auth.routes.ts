import { Routes } from '@angular/router';

export const routes: Routes = [
    {
        path: '',
        redirectTo: 'login',
        pathMatch: 'full'
    },
    {
        path: '',
        loadComponent: () => import('./auth.component'),
        children: [
            { path: 'login',        loadComponent: () => import('./login/login.component') },
            { path: 'registration', loadComponent: () => import('./registration/registration.component') }
        ]
    }
];
