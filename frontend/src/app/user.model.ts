export class User {

    private id: number;

    private email: string;

    constructor({ id, email }: { id:number, email: string }){

        this.id = id;

        this.email = email;

    }

    public getId() {

        return this.email;
    }    

    public getEmail() {

        return this.email;
    }    
}