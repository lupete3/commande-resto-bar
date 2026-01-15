<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Establishment;
use App\Models\Subscription;

#[Layout('components.layouts.app', ['title' => 'Gestion des Abonnements'])]
class SubscriptionManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $subscriptionId;

    // Form fields
    public $establishment_id;
    public $plan = 'basic';
    public $price;
    public $billing_cycle = 'monthly';
    public $started_at;
    public $ends_at;
    public $status = 'active';

    protected $rules = [
        'establishment_id' => 'required|exists:establishments,id',
        'plan' => 'required|in:basic,premium,enterprise',
        'price' => 'required|numeric|min:0',
        'billing_cycle' => 'required|in:monthly,yearly',
        'started_at' => 'required|date',
        'ends_at' => 'required|date|after:started_at',
        'status' => 'required|in:active,cancelled,expired',
    ];

    public function updatedPlan()
    {
        // Auto-set price based on plan
        $prices = [
            'basic' => 20,
            'premium' => 50,
            'enterprise' => 100,
        ];

        $this->price = $prices[$this->plan] ?? 0;
    }

    public function updatedBillingCycle()
    {
        // Adjust end date based on billing cycle
        if ($this->started_at) {
            $start = \Carbon\Carbon::parse($this->started_at);
            $this->ends_at = $this->billing_cycle === 'monthly'
                ? $start->addMonth()->format('Y-m-d')
                : $start->addYear()->format('Y-m-d');
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
        $this->started_at = now()->format('Y-m-d');
        $this->ends_at = now()->addMonth()->format('Y-m-d');
    }

    public function openEditModal($id)
    {
        $subscription = Subscription::findOrFail($id);

        $this->subscriptionId = $subscription->id;
        $this->establishment_id = $subscription->establishment_id;
        $this->plan = $subscription->plan;
        $this->price = $subscription->price;
        $this->billing_cycle = $subscription->billing_cycle;
        $this->started_at = $subscription->started_at->format('Y-m-d');
        $this->ends_at = $subscription->ends_at->format('Y-m-d');
        $this->status = $subscription->status;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'establishment_id' => $this->establishment_id,
            'plan' => $this->plan,
            'price' => $this->price,
            'billing_cycle' => $this->billing_cycle,
            'started_at' => $this->started_at,
            'ends_at' => $this->ends_at,
            'status' => $this->status,
        ];

        if ($this->editMode) {
            $subscription = Subscription::findOrFail($this->subscriptionId);
            $subscription->update($data);
            session()->flash('message', 'Abonnement mis à jour avec succès!');
        } else {
            Subscription::create($data);

            // Update establishment subscription status
            Establishment::find($this->establishment_id)->update([
                'subscription_status' => $this->status,
                'subscription_ends_at' => $this->ends_at,
            ]);

            session()->flash('message', 'Abonnement créé avec succès!');
        }

        $this->closeModal();
    }

    public function renew($id)
    {
        $subscription = Subscription::findOrFail($id);

        $newEndDate = $subscription->billing_cycle === 'monthly'
            ? now()->addMonth()
            : now()->addYear();

        $subscription->update([
            'renewed_at' => now(),
            'ends_at' => $newEndDate,
            'status' => 'active',
        ]);

        $subscription->establishment->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => $newEndDate,
        ]);

        session()->flash('message', 'Abonnement renouvelé avec succès!');
    }

    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update(['status' => 'cancelled']);

        $subscription->establishment->update([
            'subscription_status' => 'cancelled',
        ]);

        session()->flash('message', 'Abonnement annulé avec succès!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'subscriptionId',
            'establishment_id',
            'plan',
            'price',
            'billing_cycle',
            'started_at',
            'ends_at',
            'status'
        ]);
        $this->plan = 'basic';
        $this->billing_cycle = 'monthly';
        $this->status = 'active';
    }

    public function render()
    {
        $subscriptions = Subscription::with('establishment')
            ->latest()
            ->paginate(10);

        $establishments = Establishment::whereDoesntHave('subscription')
            ->orWhereHas('subscription', function ($q) {
                $q->where('status', '!=', 'active');
            })
            ->get();

        return view('livewire.super-admin.subscription-management', [
            'subscriptions' => $subscriptions,
            'establishments' => $establishments,
        ]);
    }
}
