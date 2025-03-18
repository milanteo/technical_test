import { AbstractControl, ValidationErrors, ValidatorFn } from "@angular/forms";

export const samePassword = (group: AbstractControl): ValidationErrors | null => {
        
    const password = group.get('password')?.value;

    const password_rep = group.get('password_rep')?.value;

    return (password && password_rep) && password === password_rep ? null : { passwordsMismatch: true };
    
};