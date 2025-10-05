class CartHelper {
    constructor(storageKey = 'ecomCart') {
        this.storageKey = storageKey;
        this.cart = this.loadCart();
    }

    loadCart() {
        const stored = localStorage.getItem(this.storageKey);
        return stored ? JSON.parse(stored) : [];
    }

    saveCart() {
        localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
    }

    findIndex(productId) {
        return this.cart.findIndex(item => item.id == productId);
    }

    addItem(id, name, price, quantity = 1, discount = 0) {
        id = parseInt(id);
        price = parseFloat(price);
        quantity = parseInt(quantity);
        discount = parseFloat(discount);

        const index = this.findIndex(id);
        if (index !== -1) {
            this.cart[index].quantity += quantity;
        } else {
            this.cart.push({ id, name, price, quantity, discount });
        }
        this.saveCart();
    }

    increaseQuantity(productId) {
        const index = this.findIndex(productId);
        if (index !== -1) {
            this.cart[index].quantity += 1;
            this.saveCart();
        }
    }

    decreaseQuantity(productId) {
        const index = this.findIndex(productId);
        if (index !== -1) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity -= 1;
            } else {
                this.cart.splice(index, 1);
            }
            this.saveCart();
        }
    }

    removeItem(productId) {
        const index = this.findIndex(productId);
        if (index !== -1) {
            this.cart.splice(index, 1);
            this.saveCart();
        }
    }

    emptyCart() {
        this.cart = [];
        localStorage.removeItem(this.storageKey);
    }

    getCart() {
        return this.cart;
    }

    getTotal() {
        return this.cart.reduce((total, item) => {
            const discounted = item.price - (item.discount || 0);
            return total + (discounted * item.quantity);
        }, 0).toFixed(2);
    }
}