<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart) {
        $this->session->set('cart', $cart);
    }

    public function empty() {
        $this->saveCart([]);
    }

    public function add(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
        
        $cart[$id]++ ;

        $this->saveCart($cart);
    }

    public function remove($id) {
        $cart = $this->getCart();
        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->session->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            if(!$product) {
                continue;
            }
            
            $total += $product->getPrice() * $qty;
        }
        
        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems()
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if(!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }

    public function decrement(int $id) {
        $cart = $this->getCart();
        if(!array_key_exists($id, $cart)) {
            return;
        }

        // Soit le produit est à 1 alorsi l faut le supprimer
        if($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        // Soit le produit est à plus de 1, alors il faut décrémenter:
        $cart[$id]-- ;

        $this->saveCart($cart);

    }

}
