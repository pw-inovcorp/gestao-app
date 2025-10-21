<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    //
    public function index(Request $request)
    {

        $activities = Activity::with('causer') // 'causer' é a pessoa que fez a ação
            ->latest()
            ->paginate(10);


        $formattedLogs = [];

        foreach ($activities->items() as $log) {
            $formattedLogs[] = [
                'id' => $log->id,
                'data' => $log->created_at->format('d/m/Y'),
                'hora' => $log->created_at->format('H:i:s'),
                'utilizador' => $log->causer ? $log->causer->name : 'Sistema',
                'menu' => $this->getMenuName($log->subject_type),
                'accao' => $this->getFullActionDescription($log),
                'dispositivo' => $this->getDevice($log->properties),
                'ip' => $log->properties['ip'] ?? '-',
            ];
        }

        // Substituir os dados formatados
        $activities->setCollection(collect($formattedLogs));

        return Inertia::render('Log/Index', [
            'logs' => $activities
        ]);
    }


    private function getMenuName($subjectType)
    {
        if (!$subjectType) return '-';

        // Extrair nome da classe: App\Models\Article -> Article
        $parts = explode('\\', $subjectType);
        $className = end($parts);


        if ($className === 'User') return 'Utilizadores';
        if ($className === 'Entity') return 'Entidades';
        if ($className === 'Contact') return 'Contactos';
        if ($className === 'Article') return 'Artigos';
        if ($className === 'Proposal') return 'Propostas';
        if ($className === 'Order') return 'Encomendas';
        if ($className === 'SupplierOrder') return 'Encomendas Fornecedor';
        if ($className === 'SupplierInvoice') return 'Faturas Fornecedor';

        return $className;
    }


    private function getFullActionDescription($log)
    {
        $action = $this->getActionName($log->description);
        $itemName = $this->getSubjectName($log->subject);

        if ($itemName) {
            return $action . ' ' . $itemName;
        }

        return $action;
    }


    private function getSubjectName($subject)
    {
        if (!$subject) return null;

        if (isset($subject->nome)) {
            return '"' . $subject->nome . '"';
        }

        if (isset($subject->name)) {
            return '"' . $subject->name . '"';
        }

        if (isset($subject->id)) {
            return '#' . $subject->id;
        }

        return null;
    }


    private function getActionName($description)
    {

        $description = strtolower($description);

        $description = str_replace('article', '', $description);
        $description = str_replace('entity', '', $description);
        $description = str_replace('contact', '', $description);
        $description = str_replace('proposal', '', $description);
        $description = str_replace('order', '', $description);
        $description = str_replace('user', '', $description);
        $description = str_replace('supplierorder', '', $description);
        $description = str_replace('supplierinvoice', '', $description);

        $description = trim($description);


        if ($description === 'created') return 'Criou';
        if ($description === 'updated') return 'Atualizou';
        if ($description === 'deleted') return 'Eliminou';

        return ucfirst($description);
    }


    private function getDevice($properties)
    {
        if (!isset($properties['user_agent'])) {
            return '-';
        }

        $userAgent = $properties['user_agent'];


        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';

        return 'Outro';
    }
}
