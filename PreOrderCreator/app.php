<?php

namespace Apps\PreOrderCreator;

use Sellfino\DB;
use Sellfino\Helpers;
use Sellfino\Shopify;

Class App
{

  public function install()
  {

    Shopify::hook('orders/create', 'PreOrder/webhook/orders/create', 'PreOrder');

  }

  public function uninstall()
  {

    Shopify::unhook('PreOrder/webhook/orders/create', 'PreOrder');
    
  }

  public function router($route)
  {

    switch ($route) {

      case 'orders':
      case 'products':
        $this->get($route);
        break;
      
      case 'add':
        $this->add();
        break;
      
      case 'update':
        $this->update();
        break;
      
      case 'delete':
        $this->delete();
        break;
      
      case 'webhook/orders/create':
        $this->webhook();
        break;

    }
    
  }

  public function get($handle)
  {

    $items = DB::get($handle, 'PreOrderCreator.json');
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';

    if ($search != '') {
      $items = array_filter($items, function($item) use($handle, $search) {
        if ($handle == 'orders') {
          return strpos(strtolower($item['name']), strtolower($search)) !== false;
        } else {
          return strpos(strtolower($item['id']), strtolower($search)) !== false;
        }
      });
    }

    usort($items, function($a, $b) {
      return strtotime($b["created_at"]) - strtotime($a["created_at"]);
    });

    $cut = array_slice($items, $page * 50 - 50, $page * 50);

    Helpers::json($cut);
    
  }

  public function update()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('products', 'PreOrderCreator.json');

    if (!isset($data['metafield_id'])) {
      $meta = [
        'metafield' => [
          'namespace' => 'sellfino',
          'key' => 'PreOrder',
          'value' => json_encode($data),
          'value_type' => 'json_string'
        ]
      ];

      $res = Shopify::request('products/' . $data['id'] . '/metafields', $meta, 'POST');
      $res = json_decode($res, true);

      $data['metafield_id'] = $res['metafield']['id'];

    } else {

      $meta = [
        'metafield' => [
          'id' => $data['metafield_id'],
          'value' => json_encode($data)
        ]
      ];

      Shopify::request('products/' . $data['id'] . '/metafields/' . $data['metafield_id'], $meta, 'PUT');

    }

    $found = array_search($data['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      $items[$found] = $data;

    } else {

      $data['active'] = true;
      $data['created_at'] = date('Y-m-d\TH:i:s');
      $items[] = $data;

    }

    DB::put('products', $items, 'PreOrderCreator.json');

    Helpers::success();

  }

  public function delete()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('products', 'PreOrderCreator.json');
    $found = array_search($data['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      Shopify::request('products/' . $items[$found]['id'] . '/metafields/' . $items[$found]['metafield_id'], [], 'DELETE');
      unset($items[$found]);
      DB::put('products', $items, 'PreOrderCreator.json');
      
    }

    Helpers::success();

  }
  
}