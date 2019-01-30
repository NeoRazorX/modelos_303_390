<?php
/**
 * This file is part of FacturaSctipts
 * Copyright (C) 2016-2019 Carlos Garcia Gomez <neorazorx@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of modelo_390
 *
 * @author Carlos Garcia Gomez <neorazorx@gmail.com>
 */
class modelo_390 extends fs_controller
{

    /**
     *
     * @var ejercicio
     */
    public $ejercicio;

    /**
     *
     * @var ejercicio
     */
    public $sejercicio;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Modelo 390', 'informes');
    }

    protected function private_core()
    {
        $this->ejercicio = new ejercicio();

        $this->sejercicio = $this->ejercicio->get($this->empresa->codejercicio);
        if (isset($_REQUEST['codejercicio'])) {
            $this->sejercicio = $this->ejercicio->get($_REQUEST['codejercicio']);
        }
    }

    public function desglosar_iva_compras()
    {
        $desglose_iva_compras = array();

        if ($this->db->table_exists('lineasivafactprov')) {
            $sql = "select iva,recargo,sum(neto) as neto,sum(totaliva) as totaliva,sum(totalrecargo) as totalrecargo"
                . " from lineasivafactprov where idfactura in (select idfactura from facturasprov"
                . " where fecha >= " . $this->empresa->var2str($this->sejercicio->fechainicio)
                . " and fecha <= " . $this->empresa->var2str($this->sejercicio->fechafin) . ")"
                . " group by iva,recargo order by iva asc, recargo asc;";

            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $d) {
                    $desglose_iva_compras[] = array(
                        'iva' => floatval($d['iva']),
                        'recargo' => floatval($d['recargo']),
                        'neto' => floatval($d['neto']),
                        'totaliva' => floatval($d['totaliva']),
                        'totalrecargo' => floatval($d['totalrecargo']),
                    );
                }
            }
        }

        return $desglose_iva_compras;
    }

    public function desglosar_iva_ventas()
    {
        $desglose_iva_ventas = array();

        if ($this->db->table_exists('lineasivafactcli')) {
            $sql = "select iva,recargo,sum(neto) as neto,sum(totaliva) as totaliva,sum(totalrecargo) as totalrecargo"
                . " from lineasivafactcli where idfactura in (select idfactura from facturascli"
                . " where fecha >= " . $this->empresa->var2str($this->sejercicio->fechainicio)
                . " and fecha <= " . $this->empresa->var2str($this->sejercicio->fechafin) . ")"
                . " group by iva,recargo order by iva asc, recargo asc;";

            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $d) {
                    $desglose_iva_ventas[] = array(
                        'iva' => floatval($d['iva']),
                        'recargo' => floatval($d['recargo']),
                        'neto' => floatval($d['neto']),
                        'totaliva' => floatval($d['totaliva']),
                        'totalrecargo' => floatval($d['totalrecargo']),
                    );
                }
            }
        }

        return $desglose_iva_ventas;
    }
}
