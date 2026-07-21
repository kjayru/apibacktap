<div style="max-width: 600px; margin: 0 auto; color: #1f2937;">
    @forelse ($questions as $question)
        <div style="background: #ffffff; border: 1px solid #d1d5db; margin-bottom: 20px; padding: 12px;">
            <div style="font-weight: 700; margin-bottom: 10px; line-height: 1.5;">
                {{ $question['question'] }}
            </div>

            <div>
                @foreach ($question['options'] as $option)
                    @php
                        $style = 'display: block; margin-bottom: 8px; padding: 8px 6px; border-radius: 4px; line-height: 1.35;';

                        if ($option['is_correct']) {
                            $style .= ' background: #d4edda; color: #00c851; border: 1px solid #badbcc;';
                        } elseif ($option['is_user_answer']) {
                            $style .= ' background: #f8d7da; color: #dc3545; border: 1px solid #f1aeb5;';
                        } else {
                            $style .= ' color: #1f2937;';
                        }
                    @endphp

                    <div style="{{ $style }}">
                        {{ $option['text'] }}
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div style="color: #6b7280; font-size: 14px;">
            No exam results found for this course.
        </div>
    @endforelse
</div>
