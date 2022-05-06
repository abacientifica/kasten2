export default class ServiciosGenerales {
    constructor(me) {
        this.me = me
    }

    loader() {
        return this.me.$vs.loading({
            type: 'square',
            background: '#babaea',
            color: '#fff',
            text: 'Cargando...'
        });
    }

}