<?php
class ShoppingCart {
	var $total = 0;
	var $weight = 0;
	var $point = 0;
	var $counter = 0;
	var $items = array();

	function cart() {} // constructor function

	function addItem($a){ // adds an item to cart
		if($this->counter > 0){
			foreach($this->items as $i => $item){
				if($a == $this->items[$i]){
					return ;
				}
			}
		}
		if($this->items[$a['id']]['id'] == $a['id']){
			$this->items[$a['id']]['quantity']++;
			$this->items[$a['id']]['amount'] = $a['amount'];
		}else{
			$this->items[$a['id']] = array(
				'id' => $a['id'],
				'productid' => $a['productid'],
				'title' => $a['title'],
				'price' => $a['price'],
				'point' => $a['point'],
				'quantity' => $a['quantity'],
				'weight' => $a['weight'],
				'listPrice' => $a['listPrice'],
				'amount' => $a['amount'], 
				'picture' => $a['picture'],
				'width' => $a['width'],
				'height' => $a['height'],
				'color' => $a['color'],
				'size' => $a['size'],
			);
		}
		$this->_updateTotal();
	}

	function editItem($id, $q){ // changes an items quantity
		if($q < 1) {
			$this->delItem($id);
		} else {
			$this->items[$id]['quantity'] = $q;
			$this->_updateTotal();
		}
	}


	function delItem($id){ // removes an item from cart
		$t = array();
		unset($this->items[$id]);
		$this->_updateTotal();
	}


	function emptyCart(){ // empties / resets the cart
		$this->total = $this->counter = $this->weight = 0;
		$this->items = array();
	}


	function _updateTotal(){ // internal function to update the total in the cart
		$this->weight = 0;
		$this->counter = 0;
		$this->total = 0;
		$this->point = 0;

		if(count($this->items) > 0){
			foreach($this->items as $id => $item) {
				$this->total += $item['price'] * $item['quantity'];
				$this->point += $item['point'] * $item['quantity'];
				$this->counter += $item['quantity'];
				$this->weight += $item['weight'] * $item['quantity'];
			}
		}
	}
}
?>