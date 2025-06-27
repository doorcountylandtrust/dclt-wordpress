import React from 'react';

// Minimal styling
const cardStyle = {
  border: '1px solid #e5e7eb',
  borderRadius: '0.5rem',
  padding: '1rem',
  backgroundColor: 'white',
  boxShadow: '0 1px 2px rgba(0,0,0,0.05)',
};

const containerStyle = {
  display: 'grid',
  gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
  gap: '1rem',
  padding: '1rem',
  backgroundColor: '#f9fafb',
};

export default function PreserveListView({ preserves = [] }) {
  if (!preserves.length) {
    return (
      <div style={{ padding: '1rem', fontStyle: 'italic', color: '#6b7280' }}>
        No preserves match your current filters.
      </div>
    );
  }

  return (
    <div style={containerStyle}>
      {preserves.map((preserve) => (
        <div key={preserve.id} style={cardStyle}>
          <h3 style={{ margin: '0 0 0.5rem' }}>{preserve.title.rendered}</h3>
          <p style={{ fontSize: '0.9rem', color: '#374151' }}>
            {preserve.excerpt?.rendered?.replace(/<[^>]+>/g, '') || 'No description available.'}
          </p>
          <ul style={{ fontSize: '0.85rem', color: '#4b5563', paddingLeft: '1rem' }}>
            {preserve.meta._preserve_acres && (
              <li><strong>Size:</strong> {preserve.meta._preserve_acres} acres</li>
            )}
            {preserve.meta._preserve_trail_length && (
              <li><strong>Trail:</strong> {preserve.meta._preserve_trail_length} miles</li>
            )}
            {preserve.meta._preserve_filter_difficulty && (
              <li><strong>Difficulty:</strong> {preserve.meta._preserve_filter_difficulty.join(', ')}</li>
            )}
          </ul>
        </div>
      ))}
    </div>
  );
}