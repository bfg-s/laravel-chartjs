<?php

/*
 * This file is inspired by Builder from Laravel ChartJS - Brian Faust
 */

namespace Bfg\ChartJs;

use Illuminate\Support\Arr;

class Builder
{
    /**
     * @var array
     */
    static protected $charts = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $defaults = [
        'datasets' => [],
        'labels'   => [],
        'type'     => 'line',
        'options'  => [],
        'size'     => ['width' => null, 'height' => null]
    ];

    /**
     * @var array
     */
    private $types = [
        'bar',
        'horizontalBar',
        'bubble',
        'scatter',
        'doughnut',
        'line',
        'pie',
        'polarArea',
        'radar'
    ];

    public function __construct()
    {
        $num = count(static::$charts);
        $this->name("chart$num");
    }

    /**
     * @param $name
     *
     * @return $this|Builder
     */
    public function name($name)
    {
        $old = static::$charts[$this->name] ?? [];
        $this->name = $name;
        static::$charts[$name] = array_merge($this->defaults, $old);
        return $this;
    }

    /**
     * @param $element
     *
     * @return Builder
     */
    public function element($element)
    {
        return $this->set('element', $element);
    }

    /**
     * @param array $labels
     *
     * @return Builder
     */
    public function labels(array $labels)
    {
        return $this->set('labels', $labels);
    }

    /**
     * @param array $datasets
     *
     * @return Builder
     */
    public function datasets(array $datasets)
    {
        return $this->set('datasets', $datasets);
    }

    /**
     * @param array $datasets
     *
     * @return Builder
     */
    public function simpleDatasets(string $label, array $dataset)
    {
        static::$charts[$this->name]['datasets'][] = [
            "label" => $label,
            'data' => $dataset,
        ];

        return $this;
    }

    /**
     * @param $type
     *
     * @return Builder
     */
    public function type($type)
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException('Invalid Chart type.');
        }
        return $this->set('type', $type);
    }

    /**
     * @param array $size
     *
     * @return Builder
     */
    public function size($size)
    {
        return $this->set('size', $size);
    }

    /**
     * @param array $options
     *
     * @return $this|Builder
     */
    public function options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set('options.' . $key, $value);
        }

        return $this;
    }

    /**
     *
     * @param string|array $optionsRaw
     * @return \self
     */
    public function optionsRaw($optionsRaw)
    {
        if (is_array($optionsRaw)) {
            $this->set('optionsRaw', json_encode($optionsRaw, true));
            return $this;
        }

        $this->set('optionsRaw', $optionsRaw);
        return $this;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $chart = static::$charts[$this->name];

        return view('chart-js::template')
                ->with('isNotAjax', !request()->ajax() && !request()->pjax())
                ->with('datasets', $chart['datasets'])
                ->with('element', $this->name)
                ->with('labels', $chart['labels'])
                ->with('options', $chart['options'] ?? '')
                ->with('optionsRaw', $chart['optionsRaw'] ?? '')
                ->with('type', $chart['type'])
                ->with('size', $chart['size']);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function get($key)
    {
        return Arr::get(static::$charts[$this->name], $key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|Builder
     */
    private function set($key, $value)
    {
        Arr::set(static::$charts[$this->name], $key, $value);

        return $this;
    }
}
