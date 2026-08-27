{{-- Documento de enrollment con la firma digital, equivalente al que el admin anterior
     servía en backend/users/sign.blade.php. --}}
<div class="fi-modal-content space-y-6 text-sm leading-relaxed">
    @if (! $sign)
        <p class="text-gray-500 dark:text-gray-400">
            This user has not signed the enrollment agreement yet.
        </p>
    @else
        {{-- Sin el logo del sitio: esa imagen vive en el frontend, no en este proyecto. --}}
        <div class="text-center">
            <h2 class="text-lg font-bold uppercase tracking-wide">
                Enrollment agreement and eligibility notification
            </h2>
        </div>

        <p>
            I, <strong>{{ $sign->fullname }}</strong> understand that the online course
            “Level II Non-Commissioned Officer Training Course” I am enrolling in is
            <strong>non-refundable</strong> and the cost of the course is $45.00. I am aware that no
            security agencies are guaranteed or obligated to hire me solely by me taking this course
            and that this course does not license me as a security officer. I understand that I am
            taking the above course in order to increase my knowledge in the field of security and
            only grants me a Certification of Completion. <strong>{{ $sign->initial }}</strong>
        </p>

        <div>
            <h3 class="font-bold">Notification of Eligibility:</h3>
            <p class="mt-2">
                Please review Rule 35.4’s list of disqualifying offenses and the related periods of
                ineligibility, available on the department’s website, prior to class enrollment or
                payment. You also have a right to request from the department a criminal history
                evaluation letter under Occupations Code Section 53.102. Under The Private Security
                Act (Occ. Code Chapter 1702) and Administrative Rule 35.4, a criminal conviction may
                disqualify you from a registration, commission, or license under the Act.
            </p>
        </div>

        <div>
            <h3 class="font-bold">Acknowledgment:</h3>
            <p class="mt-2">
                I <strong>{{ $sign->fullname }}</strong> confirm that I have been made aware of and
                fully understand all the information listed above and have been informed that by
                taking this training course I am not guaranteed that the licensing agency will grant
                my license.
            </p>
        </div>

        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
            <p class="font-semibold">{{ $sign->legalname }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ $sign->email }}</p>

            @if (filled($sign->firma))
                <div class="mt-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Signature</p>
                    <img src="{{ $sign->firma }}" alt="Signature" class="mt-2 h-28 w-auto">
                </div>
            @endif

            @if ($sign->created_at)
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    Signed on {{ $sign->created_at->format('M d, Y H:i') }}
                </p>
            @endif
        </div>
    @endif
</div>
