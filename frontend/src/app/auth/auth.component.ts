import { Component, inject } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import { MatButtonModule } from '@angular/material/button';
import { NavigationEnd, Router, RouterModule } from '@angular/router';
import { filter, map } from 'rxjs';

@Component({
  selector: 'app-auth',
  imports: [RouterModule, MatButtonModule],
  templateUrl: './auth.component.html',
  styleUrl: './auth.component.css'
})
export default class AuthComponent {

  router = inject(Router);

  routeTitle = toSignal(this.router.events.pipe(
    filter(e => e instanceof NavigationEnd),
    map(e => e.urlAfterRedirects)
  ), { initialValue: '' });

}
