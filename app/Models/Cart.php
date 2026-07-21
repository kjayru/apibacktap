<?php
namespace App\Models;
class Cart {
    public $cantidad;
    public $total;
    public $items = null;

    // Nuevas propiedades
    public $cupon = null;
    public $monto_descuento = 0;
    public $total_con_descuento = 0;

    public function __construct($oldcart) {
        if ($oldcart) {
            $this->items = $oldcart->items;
            $this->cantidad = $oldcart->cantidad;
            $this->total = $oldcart->total;

            // Restaurar datos del cupón si existen
            $this->cupon = $oldcart->cupon ?? null;
            $this->monto_descuento = $oldcart->monto_descuento ?? 0;
            $this->total_con_descuento = $oldcart->total_con_descuento ?? $this->total;
        }
    }

    public function add($curso, $precio, $cantidad, $id) {
        $store = [
            'id' => '',
            'curso' => $curso,
            'precio' => '',
        ];

        $this->cantidad += $cantidad;
        $this->total += $precio;
        $store['id'] = $id;
        $store['precio'] = $precio;

        $this->items[$id] = $store;

        // Actualiza total con descuento si ya había uno aplicado
        if ($this->monto_descuento > 0) {
            $this->aplicarDescuentoExistente();
        } else {
            $this->total_con_descuento = $this->total;
        }
    }

    public function removeItem($id) {
        $this->cantidad -= 1;
        $this->total -= (float) filter_var($this->items[$id]['precio'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        unset($this->items[$id]);

        // Recalcular total con descuento si es necesario
        if ($this->monto_descuento > 0) {
            $this->aplicarDescuentoExistente();
        } else {
            $this->total_con_descuento = $this->total;
        }
    }

    public function aplicarCupon($cupon_codigo, $monto_descuento) {
        $this->cupon = $cupon_codigo;
        $this->monto_descuento = $monto_descuento;
        $this->total_con_descuento = $this->total - ($this->total * $monto_descuento / 100);
    }

    private function aplicarDescuentoExistente() {
        $this->total_con_descuento = $this->total - ($this->total * $this->monto_descuento / 100);
    }
}

// class Cart{
	
// 	public $cantidad;
// 	public $total;
// 	public $items=null;
	
// 	public function __construct($oldcart){
// 		if($oldcart){
// 			$this->items = $oldcart->items;
// 			$this->cantidad = $oldcart->cantidad;
// 			$this->total = $oldcart->total;
// 		}
// 	}
// 	public function add($curso,$precio,$cantidad,$id){

// 		$store = [
// 			'id'=>'',
// 			'curso' =>$curso,
// 			'precio'=>'',
// 		];

// 		$this->cantidad +=$cantidad;
// 		$this->total +=$precio;
// 		$store['id']= $id;
// 		$store['precio']=$precio;
		
// 		$this->items[$id] = $store;

// 	}

// 	public function removeItem($id){
// 		$this->cantidad -=1;
// 		$this->total = $this->total - (float) filter_var($this->items[$id]['precio'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
// 		unset($this->items[$id]);
// 	}

	

// }