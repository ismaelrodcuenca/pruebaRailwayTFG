<x-filament-notifications::notification
    :notification="$notification"
    class="flex w-80 rounded-lg transition duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:leave-end="opacity-0"
>
    <h4>
        {{ $getTitle() }}
    </h4>

    <p>
        {{ $getDate() }}
    </p>

     <p>
       Nº Orden: {{ $workOrder->work_order_number ?? 'N/A' }}
    </p>
    <p>
       Creado por: {{ $workOrder->user->name ?? 'N/A' }}
    </p>
    <p>
       Fecha creación: {{ $workOrder->created_at ?? 'N/A' }}
    </p>
    <p>
       Status por: {{ $workOrder->statuses->last()->name ?? 'N/A' }}
    </p>

    <span x-on:click="close">
        Close
    </span>
</x-filament-notifications::notification>