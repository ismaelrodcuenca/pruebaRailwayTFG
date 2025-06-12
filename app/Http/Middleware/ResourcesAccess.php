<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionHelper;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourcesAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorisedAdminURLs =
            [
                "cash-desks",
                "clients",
                "users",
                "work-orders",
                "brands",
                "devices",
                "items",
                
                "categories",
                "repair-times",
                "taxes",
                "types",

                "companies",
                "invoices",
                "owners",
                "stores",
                
            ];
        $authorisedManagerURLs =
            [
                "cash-desks",
                "clients",
                "users",
                "work-orders",
                "brands",
                "devices",
                "items",
                "companies",
                "invoices",
                "stores",
            ];
        $authorisedSalespersonURLs =
            [
                "cash-desks",
                "clients",
                "users",
                "work-orders",
                "brands",
                "items",
                "companies",
                "invoices",
            ];
        $authorisedTechnicianURLs =
            [
                "users",
                "work-orders",
            ];
        $otherURLs =
            [
                "",
                "devices",
                "device-models",
                "dashboard",
                "login",
                "logout",
                "store-selection",
                "changeLogInOptions"
            ];
        $requestURL = $request->path();
        $segments = explode('/', $requestURL);
        $currentResource = $segments[1] ?? null;
        if (((PermissionHelper::actualRol() != ADMIN_ROL) || (PermissionHelper::actualRol() != DEVELOPER_ROL)) && $currentResource == "users") {

            $hasID = isset($segments[2]) && is_numeric($segments[2]);
            if ($hasID) {
                $userId = $segments[2];
                if (auth()->user()->id == $userId) {
                    return $next($request);
                }
            } else {
                return $next($request);
            }
        }
        if( in_array($currentResource, $otherURLs)) {
            return $next($request);
        }
        if (PermissionHelper::actualRol() == DEVELOPER_ROL) {
            return $next($request);
        }

        if (!in_array($currentResource, $authorisedAdminURLs) && !in_array($currentResource, $authorisedSalespersonURLs) && !in_array($currentResource, $authorisedTechnicianURLs)) {
            abort(403, "No tiene permisos para acceder a este recurso.");
        }
        if (PermissionHelper::actualRol() == ADMIN_ROL) {
            if (in_array($currentResource, $authorisedAdminURLs)) {
                return $next($request);
            } else {
                abort(403, "No tiene permisos.");
            }
        }
        if (!in_array($currentResource, $authorisedManagerURLs) && !in_array($currentResource, $authorisedSalespersonURLs) && !in_array($currentResource, $authorisedTechnicianURLs)) {
            abort(403, "No tiene permisos para acceder a este recurso.");
        }
        if (PermissionHelper::isManager() == MANAGER_ROL) {
            if (in_array($currentResource, $authorisedManagerURLs)) {
                return $next($request);
            } else {
                abort(403, "No tiene permisos como encargado.");
            }
        }
        if (!in_array($currentResource, $authorisedSalespersonURLs) && !in_array($currentResource, $authorisedSalespersonURLs) && !in_array($currentResource, $authorisedTechnicianURLs)) {
            abort(403, "No tiene permisos para acceder a este recurso.");
        }
        if (PermissionHelper::isSalesperson() == SALESPERSON_ROL) {
            if (in_array($currentResource, $authorisedSalespersonURLs)) {
                return $next($request);
            } else {
                abort(403, "No tiene permisos como dependiente.");
            }
        }

        if (!in_array($currentResource, $authorisedTechnicianURLs) && !in_array($currentResource, $authorisedSalespersonURLs) && !in_array($currentResource, $authorisedTechnicianURLs)) {
            abort(403, "No tiene permisos para acceder a este recurso.");
        }
        if (PermissionHelper::actualRol() == TECHNICIAN_ROL) {
            if (in_array($currentResource, $authorisedTechnicianURLs)) {
                return $next($request);
            } else {
                abort(403, "No tiene permisos como técnico.");
            }
        }

        abort(403, "No tiene permisos para acceder a este recurso.");

    }
}
