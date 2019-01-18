<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];

        foreach ($request->request->all() as $index => $value){
            $transformedInput[$transformer::originalAttribute($index)] = $value;
        }

        $request->replace($transformedInput);

        $response = $next($request);

        if(isset($response->exception) && $response->exception instanceof ValidationException){

            $data = $response->getData();

            $transformedErrrors = [];

            foreach ($data->error as $field => $error){

                $transformedField = $transformer::transformedAttribute($field);
                $transformedErrrors[$transformedField] = str_replace($field, $transformedField, $error);
            }
            $data->error =$transformedErrrors;

            $response->setData($data);
        }

        return $response;
    }
}
