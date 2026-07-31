import { AuthProvider, useAuth } from './lib/auth';
import { AuthPage } from './components/Auth';
import { StudentDashboard } from './components/StudentDashboard';
import { TeacherDashboard } from './components/TeacherDashboard';
import { BookOpen } from 'lucide-react';

function AppContent() {
  const { user, profile, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen bg-slate-950 flex items-center justify-center">
        <div className="text-center">
          <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/25">
            <BookOpen className="w-7 h-7 text-white" />
          </div>
          <div className="w-8 h-8 border-2 border-blue-500/30 border-t-blue-500 rounded-full animate-spin mx-auto" />
        </div>
      </div>
    );
  }

  if (!user || !profile) {
    return <AuthPage />;
  }

  if (profile.role === 'teacher') {
    return <TeacherDashboard />;
  }

  return <StudentDashboard />;
}

function App() {
  return (
    <AuthProvider>
      <AppContent />
    </AuthProvider>
  );
}

export default App;
