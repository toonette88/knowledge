-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 04 mars 2025 à 12:27
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `testknowledge_test`
--

-- --------------------------------------------------------

--
-- Structure de la table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stripe_payment_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `billing`
--

INSERT INTO `billing` (`id`, `order_id`, `user_id`, `stripe_payment_id`, `amount`, `created_at`) VALUES
(1, 2, 1, 12345, 100.5, '2025-02-28 21:08:01');

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Musique'),
(2, 'Informatique'),
(3, 'Jardinage'),
(4, 'Cuisine');

-- --------------------------------------------------------

--
-- Structure de la table `certification`
--

CREATE TABLE `certification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `date_obtained` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `certification`
--

INSERT INTO `certification` (`id`, `user_id`, `course_id`, `date_obtained`) VALUES
(1, 1, 1, '2025-02-28 21:08:02');

-- --------------------------------------------------------

--
-- Structure de la table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(55) NOT NULL,
  `description` longtext NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `course`
--

INSERT INTO `course` (`id`, `category_id`, `title`, `description`, `price`, `created_at`) VALUES
(1, 1, 'Cursus d’initiation à la guitare', 'Parfait pour apprendre les bases', 50, '2025-02-28 21:07:34'),
(2, 1, 'Cursus d’initiation au piano', 'Parfait pour apprendre les bases', 50, '2025-02-28 21:07:34'),
(3, 2, 'Cursus d’initiation au développement web', 'Parfait pour apprendre les bases', 60, '2025-02-28 21:07:34'),
(4, 3, 'Cursus d’initiation au jardinage', 'Parfait pour apprendre les bases', 30, '2025-02-28 21:07:34'),
(5, 4, 'Cursus d’initiation à la cuisine', 'Parfait pour apprendre les bases', 44, '2025-02-28 21:07:34'),
(6, 4, 'Cursus d’initiation à l’art du dressage culinaire', 'Parfait pour apprendre les bases', 48, '2025-02-28 21:07:34');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250210150630', '2025-02-28 21:07:27', 4143),
('DoctrineMigrations\\Version20250218201619', '2025-02-28 21:07:31', 256),
('DoctrineMigrations\\Version20250223193355', '2025-02-28 21:07:32', 167),
('DoctrineMigrations\\Version20250223200719', '2025-02-28 21:07:32', 450),
('DoctrineMigrations\\Version20250223201058', '2025-02-28 21:07:32', 317),
('DoctrineMigrations\\Version20250223201419', '2025-02-28 21:07:33', 342);

-- --------------------------------------------------------

--
-- Structure de la table `lesson`
--

CREATE TABLE `lesson` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(55) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lesson`
--

INSERT INTO `lesson` (`id`, `course_id`, `title`, `price`) VALUES
(1, 1, 'Découverte de l’instrument', 26),
(2, 1, 'Les accords et les gammes', 26),
(3, 2, 'Découverte de l’instrument', 26),
(4, 2, 'Les accords et les gammes', 26),
(5, 3, 'Les langages Html et CSS', 32),
(6, 3, 'Dynamiser votre site avec Javascript', 32),
(7, 4, 'Les outils du jardinier', 16),
(8, 4, 'Jardiner avec la lune', 16),
(9, 5, 'Les modes de cuisson', 23),
(10, 5, 'Les saveurs', 23),
(11, 6, 'Mettre en oeuvre le style dans l’assiette', 26),
(12, 6, 'Harmoniser un repas à quatre plats', 26);

-- --------------------------------------------------------

--
-- Structure de la table `lesson_content`
--

CREATE TABLE `lesson_content` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `part` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lesson_content`
--

INSERT INTO `lesson_content` (`id`, `lesson_id`, `content`, `part`) VALUES
(1, 1, 'Repellendus dolorum aut esse corporis repellat. Eum qui doloribus velit possimus doloremque consequatur cupiditate. Voluptate ut sequi officiis aut. Neque sed alias quasi consequuntur consequatur occaecati.\n\nPerferendis libero non voluptatem voluptatem iste qui. Omnis ipsum enim dicta aut nemo fugiat minima. Qui quam repudiandae blanditiis ad pariatur distinctio voluptatem. Officiis nesciunt consequuntur fuga beatae illum.\n\nMaiores aperiam nisi sit et facilis repudiandae ducimus. Illum ex non quam. Ex est aut laborum est.\n\nFugiat in aut laboriosam dolore voluptatem. Quos magni odio eos tenetur omnis sunt soluta deserunt. Dolor unde autem et mollitia. Repellat sint quibusdam asperiores nemo debitis. Modi non itaque molestiae et commodi reiciendis.\n\nId earum quidem qui commodi. Labore ipsam consectetur molestias ut. Nihil id fuga explicabo unde aperiam consequatur necessitatibus.', 1),
(2, 1, 'Sit ab blanditiis perferendis id aspernatur qui enim. Est et minima deserunt rerum atque. Voluptate eaque et eum voluptatem aperiam. Quod adipisci ab temporibus omnis sit ipsa eius.\n\nIncidunt a tenetur beatae impedit earum ipsam. Autem quam ea iure culpa. Rerum minima aut ipsam ducimus laudantium quo. Possimus magni a nisi aut ea. Non occaecati eveniet tempora quis excepturi aliquam accusamus ad.\n\nVoluptate doloremque deserunt est. In tempore necessitatibus aliquid omnis quis rerum. Minima quod mollitia ut maxime repellat.\n\nMagni suscipit iusto repellat voluptas facilis ut quos. Ad a autem eum similique non. Non earum sint occaecati nihil omnis. Temporibus maiores sequi quo eveniet quia incidunt et.\n\nNostrum dolores rem dignissimos modi accusamus non esse fugiat. Officiis exercitationem magnam at ratione. Aut unde iure qui harum dolores. Fugit veritatis et repellat minus harum est est debitis.', 2),
(3, 1, 'Et maxime quasi sed nobis. Id nemo sint provident aliquam nemo. Suscipit deleniti quisquam explicabo quia.\n\nAutem delectus illum assumenda alias recusandae. Architecto consequuntur nulla et minus. Saepe accusantium voluptatem asperiores. In nesciunt blanditiis debitis sed laborum nesciunt neque.\n\nVoluptatem eligendi necessitatibus expedita et a autem. Voluptas sapiente voluptas nobis repellendus. Dolor maiores debitis necessitatibus illo. Quas repellendus placeat ad consequatur quam et.\n\nLabore ut praesentium perspiciatis voluptatem. Natus dolores doloribus molestiae illo et enim consequatur reprehenderit. Quam id architecto magnam suscipit sunt.\n\nMinima ut quia quod sed voluptas ut. Vitae ut et asperiores id sunt. Illum aut quia explicabo aut ea sed.', 3),
(4, 2, 'Tenetur voluptas et sit sapiente culpa vitae. Nisi eligendi dolorem debitis mollitia atque recusandae. Facilis ipsum ipsam autem voluptas. Nostrum enim ratione officia asperiores.\n\nProvident libero exercitationem nulla alias perferendis. Id est facilis et totam unde quae aut. Cum possimus quis ipsa et quaerat.\n\nNatus aliquid ea sint dolores repellat quibusdam. Maiores eligendi doloremque tempore enim. Iusto non dolores alias facere molestiae sed. Eos eaque sed quasi placeat sed atque.\n\nNatus et ea sed dolorum rerum atque eos. Repellat sed ipsum nemo qui voluptatum cupiditate nihil. Cupiditate provident aliquam illo totam autem.\n\nEt fugiat illo consequatur hic magnam laboriosam. Necessitatibus iusto amet atque veritatis quo nesciunt eligendi. Possimus pariatur ratione quis minus. Voluptatum maiores eveniet blanditiis vero.', 1),
(5, 2, 'Fugiat quasi officia ipsa dolor qui. Quo totam et ad eum. Dolores voluptatem velit voluptatem reprehenderit mollitia. Repudiandae saepe aut corporis sed voluptatem illo minus. Dolorem recusandae sunt minima nostrum dicta qui.\n\nAt voluptatem consequatur et asperiores debitis ut. Ipsa at a repellat voluptatem voluptatibus.\n\nRerum ut odit aut. Veritatis nihil impedit aut maiores vitae. Aliquid fugiat itaque dolores praesentium aut. Harum quidem voluptas non.\n\nNulla incidunt facere nemo consequatur. Delectus quia ab aut eos facere. Placeat officia quisquam est eum recusandae ex et. Repellendus laudantium fugiat similique minus consequatur ea.\n\nOmnis nihil omnis aspernatur soluta. Inventore tempore aut ad pariatur voluptas sint at. Cumque voluptas explicabo qui beatae provident totam molestias.', 2),
(6, 2, 'Quia error nulla veniam unde cupiditate nam sapiente. Saepe similique quis neque. Quisquam temporibus dicta deleniti ea.\n\nUt corrupti voluptatem est aliquam. Exercitationem rerum nobis nesciunt fugit. Ipsam voluptatum voluptatum esse. Perspiciatis et deleniti quo nesciunt optio sit. Quis sed ab adipisci.\n\nCupiditate ipsum quasi consequatur. Rerum molestiae et eveniet vel. Molestias unde dolores ut molestiae.\n\nQui velit maxime et. Hic quia odit nam illo voluptatum nesciunt. Saepe facere nulla commodi sit. Voluptatem omnis iste fuga voluptatem sed aut consequatur.\n\nError est molestiae aliquid ratione. Iure odio atque laudantium quod ea cupiditate nulla. Laboriosam molestias facilis eos voluptate dolorem.', 3),
(7, 3, 'Laboriosam consequuntur enim facilis neque mollitia amet. Voluptatem laborum aperiam dolores consequatur fuga aspernatur.\n\nPerferendis voluptatibus expedita voluptas. Dolores nesciunt sit id sit sint earum labore. Commodi consectetur esse voluptas molestiae tempore.\n\nArchitecto excepturi ea consequuntur assumenda adipisci voluptate. Incidunt nesciunt quo voluptatem eum ipsa fugit aut. Sint esse et eius velit vel autem porro voluptatem. Vitae ipsam fugiat possimus facilis excepturi doloremque.\n\nUnde nihil mollitia enim. Sit distinctio enim sit dignissimos provident atque voluptatem. Aut exercitationem aspernatur enim provident est.\n\nEveniet atque necessitatibus maxime ipsam corrupti. Ut eos sequi et. Aperiam totam aut velit iste vel ratione.', 1),
(8, 3, 'Ipsam vero voluptas quo facere. Maxime impedit natus in qui. Reiciendis illum nam blanditiis itaque fugiat eos vel consequatur.\n\nRerum voluptates aut suscipit dolores numquam. Deleniti eaque et vero voluptatem nostrum. Odio enim vero commodi omnis quia atque. Nam et harum reprehenderit sit.\n\nNumquam quis maiores et pariatur est quos. Dolorem doloribus ut et odit aut nihil. Est sint in assumenda at repellat ut. Ut ut qui dolor minus qui.\n\nHic dolor facere repellat esse accusamus iste recusandae sed. Vel deserunt molestiae rerum necessitatibus et. Enim asperiores officiis maxime. Sint et unde ut qui. Nulla placeat laboriosam esse accusamus temporibus.\n\nQuia autem voluptate vel et officiis dolores architecto vitae. Sed ut at occaecati qui enim nihil et cum. Velit consequuntur porro praesentium est aut corrupti omnis est. Nihil velit aut omnis doloremque unde eaque nihil.', 2),
(9, 3, 'Ipsum deleniti molestiae in ratione corrupti. Exercitationem quaerat perferendis dignissimos temporibus. Et ea consectetur voluptatem doloribus aut itaque. Explicabo ut a totam a et.\n\nNostrum omnis blanditiis nihil voluptates accusantium id aliquid. In qui officia eum quibusdam. Recusandae incidunt commodi iusto dolorem temporibus. In harum sed libero deserunt maxime.\n\nAd fugiat aperiam quia ea. Magnam voluptate est earum iste laudantium. Aut autem minima et.\n\nEius cumque qui sequi et. Voluptatem delectus quia est ex ab cum. Dolorum voluptas provident aut.\n\nUt laudantium voluptate adipisci dolorem voluptatem. Qui soluta qui dolorem sit ipsum ut. Nihil nam et et dicta alias et.', 3),
(10, 4, 'Omnis soluta maxime dolore facere libero sunt qui. Sit et impedit amet ut similique. Qui voluptate ut et ut consequatur dolore.\n\nUllam dicta dolore et. Aut non cupiditate aut velit eaque suscipit corporis dicta.\n\nNeque nulla expedita in id similique laboriosam omnis. Aut quidem qui in ipsum. Fuga minima eos ipsa quos quia. Quos sed iusto ut aspernatur tempore tempore.\n\nSit ut enim nemo velit nihil accusamus soluta iure. Et velit ea sequi. Qui non aperiam accusamus animi voluptatem. Cum officia maxime eum quisquam.\n\nRerum dicta alias optio dolorum doloribus et laboriosam ut. Provident nobis aut et quo. Odit quos quam ad ut molestiae tenetur ut. Omnis earum id voluptas perspiciatis animi facilis aliquid et.', 1),
(11, 4, 'Vitae placeat error facilis aperiam quos. Delectus omnis quidem repellat dolore. Cumque sit et impedit incidunt sequi beatae.\n\nAsperiores amet et totam exercitationem nesciunt dicta eum. Ut labore ullam distinctio facilis itaque et maiores. Provident veniam esse distinctio qui quia.\n\nEst sequi ut rerum dolorem. Sequi illo consequatur voluptas. Eum sit tempore esse suscipit ea consequatur. Nesciunt fuga ut voluptatem nulla.\n\nEt delectus quae est error consequatur dolorum. Ratione non eos molestiae et laudantium et dolorum voluptatem.\n\nDolores consectetur qui quia quas voluptatem. Repellendus culpa consequuntur consequatur quia. Sed nisi est molestias officiis iure et.', 2),
(12, 4, 'Et autem architecto cum possimus hic nihil corporis voluptatem. Quos voluptatem possimus qui qui architecto. Qui voluptate et dolores assumenda ut.\n\nCorrupti laudantium aliquid illum quidem libero et nostrum. Velit dolore voluptatem nihil sint reprehenderit expedita dignissimos. Officia illum ut quae rerum in quasi eaque. Nostrum iure consequatur aspernatur quia itaque magnam optio est.\n\nEt sapiente eos doloremque ad delectus dolore harum. Ullam voluptas doloremque itaque iusto et itaque ad molestias. Autem porro in quidem consequatur.\n\nEx aut et sequi veniam aliquid dolor veniam doloribus. Et aut id quam. Accusantium mollitia est eos quis magnam consequatur provident est.\n\nMaxime ad et atque dolores tempore. Accusamus nulla sed non quibusdam. Laudantium similique ut a ad fuga. Laudantium quia consequatur velit aut nihil earum.', 3),
(13, 5, 'Quas ea quae quis. Omnis eius eligendi consequatur et. Hic quia id illum quidem minima voluptatem. Veritatis corporis sed voluptate quidem necessitatibus.\n\nHic est et qui quod quis nulla est. Corrupti omnis ut aut animi neque vel et. Nihil ut magnam aperiam sit molestiae. Placeat possimus autem quia amet ut quaerat quibusdam rerum.\n\nVoluptates ad et ut doloribus quisquam. Delectus aut ad aut voluptatem et dolores nemo et. Ex nobis commodi quis rerum sint ducimus dolorum.\n\nVoluptate voluptates voluptatibus omnis tempora. Ea consequatur enim dolor aut eum. Aut et in qui cumque.\n\nEarum reprehenderit et ea molestiae atque doloremque nihil. Repellendus earum cupiditate eius quia. Reprehenderit omnis consequatur consectetur et. Repellat in est id et tempore cum.', 1),
(14, 5, 'Nemo aut qui similique ratione voluptates praesentium tenetur. Corporis ut qui sint. Ea dolor neque eos voluptatem.\n\nQui tempore voluptatum unde enim. Et rerum voluptate maxime officiis. Doloribus nostrum distinctio dolorum blanditiis voluptatem.\n\nQuia enim voluptatibus laudantium excepturi ad ad dolorem. Et animi rerum inventore saepe minus sed est beatae. Ut sed placeat sit laudantium.\n\nVoluptates error sint nesciunt enim beatae dicta itaque. Numquam et voluptatem quae quis quia ipsum. Aspernatur quo nisi doloribus quibusdam repellendus.\n\nDelectus et officiis aut accusamus ea. Nam labore ad corrupti ut aut blanditiis exercitationem. Aut dolore labore natus ipsam omnis reprehenderit accusantium animi. Sunt sed nostrum qui corporis quo perspiciatis.', 2),
(15, 5, 'Voluptatem cupiditate dolor non aut nostrum. Facilis eum ipsam officia commodi. In perspiciatis earum laudantium similique beatae eaque occaecati magnam. Cupiditate dolorem minus perspiciatis ad sed nihil sit.\n\nCupiditate magnam sit accusantium totam est. Quae dicta fugiat alias illo facilis quae voluptates. Voluptas placeat qui recusandae qui qui et. Culpa qui ut recusandae eaque ut cupiditate ullam. Alias suscipit ut omnis sunt iure voluptate.\n\nIure magni eos et. Veritatis earum voluptatem non in. Omnis ab quibusdam consequatur.\n\nAlias repellat veniam eaque error at. Aut neque nulla non dicta omnis ducimus neque. Aut vel voluptatum quia nemo velit et adipisci sed. Iste esse debitis dolore.\n\nIusto molestias est enim et ipsum voluptatem velit qui. Libero voluptatem beatae explicabo optio. Ut quidem architecto et eum nemo perspiciatis quibusdam. Tenetur fuga quam occaecati laborum vero mollitia facilis.', 3),
(16, 6, 'Velit culpa iusto aliquid dolor. Sed sunt qui dolorum exercitationem nihil. Qui totam aut consequatur odit illum et cumque. Sunt culpa cupiditate deserunt vel consectetur. Quaerat ratione ut ut enim suscipit.\n\nNesciunt repellat magnam sunt recusandae eveniet. Voluptatum dolorem non voluptatem incidunt. Sed consequatur ut autem sunt debitis ut accusamus.\n\nOmnis harum sapiente optio accusantium et. Corporis dignissimos maiores tempore in quis sint dignissimos.\n\nConsectetur hic delectus pariatur quaerat minima aliquid. Voluptatem necessitatibus explicabo omnis. Cum aut facere qui nihil. Neque non ipsa sit cupiditate doloremque vel distinctio.\n\nVel sunt beatae sit error temporibus vel vel. Excepturi qui consequatur nostrum laborum ipsum quia optio. Quis atque ab iste debitis. Optio hic vel molestias dolor.', 1),
(17, 6, 'Velit corrupti magni consequatur voluptates ut minus porro. Aut nemo et minima cum ducimus voluptatibus laudantium. Enim rerum possimus consectetur culpa. Quae est incidunt id voluptatem voluptatem et.\n\nModi nihil fuga illo et. Commodi est eaque quod laboriosam. Adipisci consequuntur aut nobis distinctio dolorem aliquam. Beatae voluptatem quidem autem ea minima qui ex.\n\nQui voluptatibus ex eligendi tempora vel. Consequuntur iste laudantium modi labore dolor aspernatur. Quia enim delectus voluptatem aut delectus fuga accusantium at. Et est rerum culpa similique inventore animi.\n\nVero quia perferendis atque delectus. Exercitationem autem voluptas rerum et deleniti. Quo voluptas quidem in pariatur.\n\nFuga delectus molestiae distinctio quis. Libero necessitatibus quae aspernatur sit. Veritatis quibusdam dolor et consectetur. Explicabo nesciunt est inventore perferendis natus.', 2),
(18, 6, 'Est sit possimus nesciunt qui qui similique voluptatem. Numquam quis a et sunt rerum quia dolores. A voluptatem nam eum dicta. Eligendi dolorum et aut in atque nobis sunt quae.\n\nProvident voluptatem temporibus repellendus excepturi vero dicta aut. Maxime molestiae molestiae repellendus cupiditate tenetur. Voluptatem cupiditate autem harum dignissimos voluptas accusantium.\n\nUt in quo quo molestiae. Occaecati cum id eveniet. Ut vel maxime nobis et. Sit voluptatibus velit facere.\n\nId non nisi sint fugit et accusamus. Numquam enim expedita id non aut et saepe eos.\n\nTemporibus voluptatum ut dolores sequi et quis et. Quia quasi veniam corrupti non voluptatum et qui. Quibusdam maxime soluta illum modi.', 3),
(19, 7, 'Voluptate ut autem ut illum eum praesentium autem. Totam est fuga minima sit maxime non. Autem quidem omnis voluptas qui error delectus quam. Sequi id aut est veritatis.\n\nDolor itaque a neque aut minima quasi rem. Deleniti quia omnis neque velit velit sed non. Quia libero distinctio voluptate dicta iste. Inventore voluptate velit laudantium iste sequi error. Veritatis dolore culpa blanditiis temporibus expedita.\n\nEst aut fuga perspiciatis blanditiis nemo impedit explicabo laudantium. Deserunt fugiat qui molestiae voluptates hic sit qui. Rerum perferendis repellendus rerum. Veritatis quibusdam et accusamus ipsum aut eum adipisci. Aut magni sint labore nostrum non quo.\n\nFacilis qui quam ut incidunt fugit qui aperiam fugiat. Nisi et omnis aut fugit. Aperiam aut mollitia aspernatur quaerat pariatur. Commodi eos officiis consequuntur.\n\nTemporibus qui excepturi aut expedita reprehenderit quia vel. Voluptate iste quia voluptatem ut eum expedita et voluptatibus. Molestiae neque numquam veritatis voluptatum dolorem. Saepe quas aut ipsam expedita. Molestias autem sapiente sed repudiandae.', 1),
(20, 7, 'At aperiam vitae itaque ea ex eum aliquam facilis. Veritatis recusandae cum consequatur et minima labore et. Unde quo unde pariatur alias repellendus eveniet. Eveniet voluptatem debitis et consequatur quam iusto.\n\nCorporis ad quas suscipit pariatur. Maiores placeat quidem sit alias aut. Provident et consequatur blanditiis quisquam. Qui qui esse fugiat occaecati voluptates quod.\n\nAsperiores est voluptatum quas nihil et magni perspiciatis. Id earum voluptatum non dolores placeat eaque magni. Error commodi excepturi maxime sunt. Eveniet impedit eligendi ratione neque accusamus minima.\n\nUt ipsa explicabo asperiores accusamus consequuntur. Alias qui perferendis ea. Voluptatum autem in tenetur esse.\n\nSed voluptatum sed praesentium id molestiae accusantium. Aut ratione impedit illum qui vel incidunt dolorem. Expedita cupiditate cumque corporis eum qui necessitatibus est.', 2),
(21, 7, 'Et delectus in fugiat velit. Illo quisquam libero consequatur sit autem occaecati consequuntur et.\n\nDolor inventore quo dolorem qui porro illo. Hic provident aliquid nisi et et dolor.\n\nTenetur in dignissimos ut numquam quam aliquid. Quo ex at quod iste. Sit occaecati nobis et asperiores. Esse quia nesciunt omnis quod dolore.\n\nAlias voluptatem similique molestiae sit. Illum veritatis placeat sunt quas quis voluptas architecto. Error cumque itaque quia et nemo.\n\nEx eligendi illo autem. Modi natus sint nihil delectus in. Non eum unde ut.', 3),
(22, 8, 'Omnis ullam sunt vitae quibusdam qui. In voluptatem dolores nisi excepturi unde amet. Non non occaecati itaque voluptatem.\n\nDelectus debitis omnis fuga fugiat labore. Ut blanditiis qui velit. Eos in praesentium dolorum qui officiis accusantium.\n\nConsequatur quae consequatur est voluptas repellendus. Voluptatibus nesciunt hic cupiditate magnam sint iste voluptatum. Repellendus nam velit qui aut et delectus officiis.\n\nNulla et corrupti aspernatur labore. Praesentium optio dolor quasi dicta et velit quibusdam. Laborum nisi enim reprehenderit voluptates excepturi sit. Sit ut sapiente est quidem quam quis excepturi. Dolore perspiciatis delectus velit unde cum inventore dolorem.\n\nMaxime autem nemo culpa consequatur. Molestiae non voluptas facilis magnam nobis sit asperiores. Ipsa cumque tempore molestiae debitis soluta nihil similique. In expedita consequatur ut perferendis id dolor.', 1),
(23, 8, 'Modi aut voluptas tenetur harum in voluptas quis. Dolores et molestiae asperiores est aut et. Laborum consequatur consectetur error aut. Reiciendis voluptatem quis doloremque explicabo dolores.\n\nSunt voluptas et delectus rerum optio. Dolor sapiente officiis magni officiis autem maiores. Repudiandae et occaecati in animi voluptas quidem labore.\n\nFacere rerum commodi quod hic magni. Voluptatibus ex voluptas rerum excepturi incidunt voluptate nihil. Voluptate earum sapiente velit in non. Nobis magni quis reiciendis dolor.\n\nEt nemo non iure accusantium. Aut nobis soluta beatae rem. Eaque natus autem et doloremque ut autem consectetur. Distinctio quos nulla eos saepe consequatur eaque quae.\n\nRecusandae dolor consectetur non atque voluptatem. Adipisci molestiae quibusdam et ea asperiores dolore.', 2),
(24, 8, 'Dignissimos laborum perferendis omnis consequatur quibusdam a. Unde iure sapiente sit perspiciatis.\n\nQuae illo qui et tempore odit. Error ea fugit eos aperiam fugiat ea numquam. Ab modi voluptatem hic nihil. Et voluptatibus quae laudantium odio.\n\nEaque blanditiis dolorem dolor repellat. Molestiae mollitia error nobis facilis et consequatur dolore maxime. Animi magni quod laboriosam aut.\n\nSequi aut doloremque facilis eum voluptate iure. Deleniti dolor omnis ut officiis inventore aperiam voluptas. Id labore voluptatem autem.\n\nVoluptas distinctio inventore et nemo sint iure. Mollitia vel et velit voluptate dolores ipsum. Nulla dolor fugit deserunt.', 3),
(25, 9, 'Velit molestiae quo sunt occaecati corporis quae. Qui aperiam porro dolores et eligendi voluptatem ut.\n\nAutem voluptatem in dolorem assumenda. Ratione maxime repellendus exercitationem odio beatae quia magnam qui. Veritatis impedit est at aut molestias commodi mollitia. Dolor debitis nisi reprehenderit modi.\n\nNon modi illo laboriosam. Debitis aspernatur eos eveniet magni ad. Deleniti et modi aut. Reprehenderit sit saepe eligendi est.\n\nVoluptatum velit vel rem in et nisi. Et quasi est repellat hic natus mollitia est. Dicta eum praesentium repudiandae repellendus vel a laboriosam vel. Repudiandae dolore deleniti non corporis possimus libero distinctio.\n\nItaque quod quidem natus aspernatur et. Explicabo fugit quod ex sit aliquid dolorem. Nulla quaerat tempora ut et quia et. Quo est ad ad earum.', 1),
(26, 9, 'Magnam suscipit iure eum vero error deleniti nostrum cupiditate. Minima eos qui esse quis. Consequatur neque consequatur doloribus ullam. Repellendus ipsa consectetur commodi rerum dolores eaque.\n\nNesciunt aut odit accusantium molestias quasi. Occaecati et explicabo ratione repudiandae in in incidunt. Et molestias mollitia iusto molestiae earum exercitationem omnis.\n\nOccaecati id vero nostrum sit eius voluptas et. Enim et vero qui neque et odit. Magnam illum eum quas quisquam placeat ea.\n\nNulla illo cum excepturi ipsam necessitatibus aperiam culpa. Vero perspiciatis velit quia culpa voluptatum enim omnis. Molestias facilis dicta quo deleniti.\n\nMolestiae provident ipsa nemo magni. Qui maiores et laboriosam alias veritatis magnam consectetur. Sunt qui vitae et.', 2),
(27, 9, 'Et adipisci non qui labore nostrum et nobis. Dolorum assumenda excepturi neque dolorum aut minus. Id quam enim corrupti et. Error dignissimos dignissimos perspiciatis impedit laboriosam modi eligendi.\n\nIpsum maiores reiciendis autem reiciendis laborum corrupti et dolor. Et qui omnis hic voluptatem harum aut aut. Vero veniam provident ducimus qui officiis dolor excepturi. Ut voluptas est itaque alias dignissimos rerum rerum quidem.\n\nFuga similique dolorem qui. In neque voluptatem ut amet. Natus reprehenderit sit eveniet est aperiam numquam id dicta. Rerum ut dolorem qui nisi quam aliquid.\n\nTenetur impedit architecto est repellendus cum quasi. Error officiis alias porro tempora ratione officia. Maxime occaecati repudiandae aut voluptates quia. Voluptatem aliquam asperiores sed illo id ducimus.\n\nSit itaque voluptatum est temporibus voluptatem nam. Deserunt veniam et id tenetur dolor blanditiis dolores. Blanditiis sed quo ea ut officiis et temporibus. Dolorem quidem id laboriosam laudantium aut libero modi consequatur.', 3),
(28, 10, 'Repudiandae voluptatem pariatur vel velit eaque. Ut illum magni minus laborum porro hic est ipsa. Deleniti quos excepturi est unde adipisci laudantium. Et id tenetur facere at numquam qui odit.\n\nOmnis cumque ab est eaque culpa. Ut corrupti voluptas harum ex perspiciatis quis occaecati quas. Est neque quos optio nulla ut. In vero sed fuga sed odit.\n\nOptio mollitia tenetur eius placeat nesciunt. Pariatur voluptates est minima asperiores. Incidunt necessitatibus unde aut praesentium dolore aut.\n\nPorro fugit ex sed et laborum et. Nesciunt nihil architecto in et. Natus autem laudantium aut. Fugiat aliquid natus nostrum eaque quis pariatur eaque.\n\nVeritatis dolores sint voluptates praesentium. Libero inventore tempore deserunt et et expedita magnam. Dolores dolor adipisci dolorum numquam.', 1),
(29, 10, 'Eaque necessitatibus voluptatem iusto non harum quia. Sed earum rerum reiciendis a dolorem pariatur animi. Modi aliquid velit dignissimos aut eligendi illum quos. Reiciendis architecto harum dolorem est distinctio vel aut.\n\nUt vel provident quaerat animi enim. Quisquam occaecati commodi veritatis voluptas ducimus est eos quidem. Tempore possimus cum ut aperiam ut perferendis aut. Omnis porro aspernatur vel sint.\n\nExcepturi quo laudantium qui iste non. Dolores dicta corrupti inventore enim. Quia laborum quia voluptate laborum quis. Aut nostrum eum enim tenetur sapiente. Alias omnis voluptatem nam animi.\n\nSaepe animi sapiente expedita ut veniam maiores inventore ea. Natus repudiandae aut quis omnis. Suscipit dolorem quo pariatur qui explicabo iste tempora. Omnis commodi quis laudantium beatae rem sunt.\n\nIncidunt laboriosam beatae numquam culpa sed aliquam ratione esse. Ad ea nihil nobis id nisi totam ut. Sit et qui similique sequi cupiditate sit eius.', 2),
(30, 10, 'Iste iste voluptatibus aut et impedit et. Unde ipsam tempora velit. Libero ut recusandae beatae qui maiores tempore. Est ipsum eveniet voluptatem sit eos.\n\nNon consequatur enim occaecati est eligendi. Illum quia mollitia ratione porro cumque voluptatibus. Perspiciatis iusto corrupti et et cumque dolore adipisci. Cupiditate voluptate ratione molestias vel aut dolore.\n\nAut doloribus dolore atque blanditiis soluta saepe. Quas ut consequatur voluptas tempora ut odit tenetur molestias. Molestiae sapiente ea est molestiae earum exercitationem.\n\nVoluptas eos quaerat reprehenderit aliquid. Aliquam blanditiis ea dicta et consectetur et. Voluptatem nihil doloribus provident occaecati architecto vel quia.\n\nIllo voluptas rem dolor nihil. Animi quaerat quos qui iusto eum quos. Corrupti eius maxime quibusdam soluta quidem amet.', 3),
(31, 11, 'Recusandae aut et quia eum labore debitis distinctio. Rerum non eum aliquam sit dolor. Ipsum vel est est eveniet aut cupiditate laborum.\n\nPlaceat optio provident suscipit consequuntur sit ad cumque. Vel veniam dolores similique doloremque. Cum natus maxime quos quasi velit consequuntur perspiciatis.\n\nVoluptas deserunt consequatur atque eum aliquid aspernatur. Rerum et ut tempora vel assumenda. Suscipit et consequuntur fugit itaque. Architecto ratione deserunt ut recusandae.\n\nCorporis aut eius laudantium quia. Amet quia eos delectus quisquam et et dolorum. Assumenda aliquam ipsam consequatur sint. Esse eligendi blanditiis consequatur similique facilis praesentium eum.\n\nAdipisci ut id ea quisquam exercitationem. In atque quis laborum officiis libero corrupti. Minima vel et minus veritatis. Similique odio veritatis qui consequatur in odit iste.', 1),
(32, 11, 'Qui rem quisquam sunt ipsum earum voluptatem porro numquam. Esse aut non aut et. Quis voluptas sed dolores ut. Nobis vero voluptates voluptatibus voluptas.\n\nEnim est eum voluptatum minima dicta rerum. Et eaque veniam accusamus blanditiis nulla. Placeat animi assumenda nemo suscipit. Minus a et quo deleniti.\n\nNon enim voluptas ut natus. Voluptas voluptatum veritatis eos quibusdam. Veritatis veniam nam dolore corporis ut distinctio. Deserunt amet non et ea et et ipsam sit.\n\nAmet delectus perspiciatis eum. Velit sit nihil rerum est nisi.\n\nQui voluptas esse eos eius totam aliquam. Voluptatum est explicabo error tempore quas dolorum. Consequuntur dignissimos sapiente dolores ullam in fugit aliquid molestiae.', 2),
(33, 11, 'Nihil non dolorem fugit inventore sit similique est. Eum sed sint voluptatem est expedita.\n\nAt amet et aut quo ducimus possimus aut. Similique qui illo nisi perspiciatis.\n\nAsperiores numquam ipsam nihil ut aliquam sequi. Voluptatem natus dicta animi optio nisi quis. Rerum magni ut accusantium corporis. Excepturi qui esse dignissimos. Porro sapiente omnis laboriosam qui aut hic.\n\nMagni quia vero odit qui ea libero. Molestiae officiis nisi ut qui facere odit nihil. Quasi in magni mollitia occaecati dolores.\n\nAb consequatur unde illum. At facilis minus soluta. Tempore autem eaque cum id. Velit aut quod error a cumque qui velit aut.', 3),
(34, 12, 'Nisi aliquid ducimus laudantium in. Inventore consequatur est praesentium est tempore labore. Qui dolorum officiis perspiciatis similique molestias quidem. Quia facere possimus aperiam molestias sed. Quae blanditiis alias quidem itaque ab.\n\nCorrupti id iusto sapiente voluptatem quaerat eveniet cum atque. Sunt ipsam corporis est placeat.\n\nVoluptas velit incidunt eveniet. Molestiae omnis odio fuga. Modi debitis et quas nulla amet est.\n\nPossimus id distinctio sunt corrupti maiores. Corrupti qui nihil explicabo suscipit et dicta deleniti.\n\nEos deserunt molestiae dignissimos inventore earum. Ut nostrum pariatur dolorum labore voluptatibus illo.', 1),
(35, 12, 'Consectetur consequatur voluptas sint nostrum rerum ducimus harum. Ipsam quos eum placeat in. Ducimus cupiditate magni possimus amet. Facere autem aliquid deserunt magnam ut.\n\nEst blanditiis et vero quidem reprehenderit. Quos non repudiandae in sit. Itaque sed error et ullam id.\n\nEum hic temporibus nesciunt ut sed odio. Qui voluptatem et quam reiciendis similique soluta. Consectetur illum magnam error quae.\n\nNumquam sit accusantium vero corporis ipsam ullam omnis. Et sapiente voluptates incidunt omnis.\n\nOccaecati labore molestiae ab qui quasi. Omnis qui dicta repellendus consequuntur. Tempora ut quibusdam et dolorum necessitatibus rerum ad. Voluptas quia sed aut voluptas et omnis rerum.', 2),
(36, 12, 'Magni quia suscipit ex explicabo. Quasi ipsa natus ut quaerat vel tempore dolore maiores. Sit illum magni nisi officiis voluptatem. Dolores rem itaque veritatis enim laboriosam reiciendis.\n\nQui eum aut exercitationem soluta. Quo ullam ducimus dicta. Voluptatibus quae possimus pariatur odit quia. Sit rerum asperiores exercitationem beatae quam nostrum.\n\nDoloribus incidunt sit eos praesentium vel dignissimos. Voluptatem aut labore ut sed autem. Qui ea et id. Nihil eius dicta omnis quisquam necessitatibus. Debitis modi inventore nisi.\n\nEst in numquam aut. Natus et fugiat suscipit ut qui sit sed odio. Magni cumque unde sint distinctio consequatur assumenda.\n\nEum accusamus voluptatem aut. Optio dicta vitae est dolores sed quis. Vel quia deserunt consequatur tempora qui voluptas minima. Dolorum porro suscipit iste ea tenetur officiis alias.', 3);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` double NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `user_id`, `total`, `status`, `created_at`) VALUES
(1, 9, 44, 'En attente', '2025-02-28 21:07:57'),
(2, 1, 100.5, 'En attente', '2025-02-28 21:08:01'),
(3, 1, 150.75, 'En attente', '2025-02-28 21:08:02');

-- --------------------------------------------------------

--
-- Structure de la table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `unit_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `course_id`, `lesson_id`, `unit_price`) VALUES
(1, 1, 5, NULL, 44),
(2, 3, NULL, 1, 50);

-- --------------------------------------------------------

--
-- Structure de la table `progression`
--

CREATE TABLE `progression` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `chapter` int(11) DEFAULT NULL,
  `percentage` int(11) DEFAULT NULL,
  `lesson_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `progression`
--

INSERT INTO `progression` (`id`, `user_id`, `course_id`, `chapter`, `percentage`, `lesson_id`) VALUES
(1, 1, NULL, 1, 20, 8),
(2, 1, NULL, 2, 5, 5),
(3, 1, NULL, 4, 6, 12),
(4, 2, NULL, 3, 52, 6),
(5, 2, NULL, 5, 19, 10),
(6, 2, NULL, 1, 99, 2),
(7, 3, NULL, 2, 11, 10),
(8, 3, NULL, 3, 23, 2),
(9, 3, NULL, 2, 9, 1),
(10, 4, NULL, 4, 14, 7),
(11, 4, NULL, 3, 0, 9),
(12, 4, NULL, 3, 89, 8),
(13, 5, NULL, 5, 18, 1),
(14, 5, NULL, 3, 45, 4),
(15, 5, NULL, 1, 67, 3),
(16, 6, NULL, 3, 1, 6),
(17, 6, NULL, 2, 88, 8),
(18, 6, NULL, 5, 36, 12),
(19, 7, NULL, 2, 47, 7),
(20, 7, NULL, 4, 25, 5),
(21, 7, NULL, 1, 95, 9),
(25, 9, NULL, 4, 19, 3),
(26, 9, NULL, 1, 53, 1),
(27, 9, NULL, 1, 41, 10),
(28, 10, NULL, 3, 4, 6),
(29, 10, NULL, 3, 82, 6),
(30, 10, NULL, 4, 81, 4),
(31, 11, NULL, 3, 29, 9),
(32, 11, NULL, 4, 54, 7),
(33, 11, NULL, 2, 38, 11),
(34, 4, NULL, 3, 100, 10);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `name` varchar(55) NOT NULL,
  `firstname` varchar(55) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`, `firstname`, `created_at`, `is_verified`) VALUES
(1, 'user0@example.fr', '[\"ROLE_USER\"]', '$2y$04$BS1d0cacX0y950C/dp4TveOGYBgax.9sW/r0mMEObcvuesxZlppi2', 'user0', 'firstname0', '2025-02-28 21:07:34', 0),
(2, 'user1@example.fr', '[\"ROLE_USER\"]', '$2y$04$pa5AOvmUbBFD6HJZraHcsO1.CxUN2diyga7.yu1OB9Xb7dqApd7ZC', 'user1', 'firstname1', '2025-02-28 21:07:34', 0),
(3, 'user2@example.fr', '[\"ROLE_USER\"]', '$2y$04$qv/8pY24a8B.t1T7S94yVeYOqTiHtXJWSRB8myeVbbhmNkI8FDFdq', 'user2', 'firstname2', '2025-02-28 21:07:34', 0),
(4, 'user3@example.fr', '[\"ROLE_USER\"]', '$2y$04$mPOeYeaQLR61fNHPa0T3J.Nm/ld0XT35o5ur2An2MZvzWWJ73soAW', 'user3', 'firstname3', '2025-02-28 21:07:34', 0),
(5, 'user4@example.fr', '[\"ROLE_USER\"]', '$2y$04$A/x071zhufJzeOY.o/DnKev/MZG0UXH7MJODtItrENMMIfeqZsETG', 'user4', 'firstname4', '2025-02-28 21:07:34', 0),
(6, 'user5@example.fr', '[\"ROLE_USER\"]', '$2y$04$atSy0HiU.ZnRP7IAXDvKg.bl8mxAMaY7MJwbsf9i.HCvkziJ9pLFW', 'user5', 'firstname5', '2025-02-28 21:07:34', 0),
(7, 'user6@example.fr', '[\"ROLE_USER\"]', '$2y$04$DK.pzd6Kd9tjB6r/h/3gWOIXUYGwFj5oaD1sFOubjDV9x4Hy7O2yq', 'user6', 'firstname6', '2025-02-28 21:07:34', 0),
(9, 'user8@example.fr', '[\"ROLE_USER\"]', '$2y$04$iitZcEC.NYWGpFL/g0YTcOtma7qdnCoV8KjZ0Htu7d3a/3cdr3FC2', 'user8', 'firstname8', '2025-02-28 21:07:34', 0),
(10, 'user9@example.fr', '[\"ROLE_USER\"]', '$2y$04$UwsfQG1VUkhfOje6SqoOQ.9LFZSIljd/27maWfVHpT2rlq29mP84S', 'user9', 'firstname9', '2025-02-28 21:07:34', 0),
(11, 'admin@example.fr', '[\"ROLE_ADMIN\"]', '$2y$04$Kl.xtUvoBSpyNsBlp9MN0uiUtaP8nvM1yLBG4D2u7XGl3P1cQO0O.', 'admin', 'admin', '2025-02-28 21:07:34', 0),
(12, 'newuser@example.com', '[]', '$2y$04$PY8WbMbyStN.fpIW5.T4Y.y08.QCILVRyGZWoZ3TkjWqAmmw.rH0.', 'newuser', 'newuserfirstname', '2025-02-28 21:07:59', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_EC224CAA8D9F6D38` (`order_id`),
  ADD UNIQUE KEY `UNIQ_EC224CAAA76ED395` (`user_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `certification`
--
ALTER TABLE `certification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6C3C6D75A76ED395` (`user_id`),
  ADD KEY `IDX_6C3C6D75591CC992` (`course_id`);

--
-- Index pour la table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_169E6FB912469DE2` (`category_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F87474F3591CC992` (`course_id`);

--
-- Index pour la table `lesson_content`
--
ALTER TABLE `lesson_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BB9620ECDF80196` (`lesson_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F5299398A76ED395` (`user_id`);

--
-- Index pour la table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_ED896F468D9F6D38` (`order_id`),
  ADD KEY `IDX_ED896F46591CC992` (`course_id`),
  ADD KEY `IDX_ED896F46CDF80196` (`lesson_id`);

--
-- Index pour la table `progression`
--
ALTER TABLE `progression`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D5B25073A76ED395` (`user_id`),
  ADD KEY `IDX_D5B25073591CC992` (`course_id`),
  ADD KEY `IDX_D5B25073CDF80196` (`lesson_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `certification`
--
ALTER TABLE `certification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `lesson`
--
ALTER TABLE `lesson`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `lesson_content`
--
ALTER TABLE `lesson_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `progression`
--
ALTER TABLE `progression`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `FK_EC224CAA8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  ADD CONSTRAINT `FK_EC224CAAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `certification`
--
ALTER TABLE `certification`
  ADD CONSTRAINT `FK_6C3C6D75591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `FK_6C3C6D75A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `FK_169E6FB912469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `lesson`
--
ALTER TABLE `lesson`
  ADD CONSTRAINT `FK_F87474F3591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Contraintes pour la table `lesson_content`
--
ALTER TABLE `lesson_content`
  ADD CONSTRAINT `FK_BB9620ECDF80196` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `FK_ED896F46591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `FK_ED896F468D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  ADD CONSTRAINT `FK_ED896F46CDF80196` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`);

--
-- Contraintes pour la table `progression`
--
ALTER TABLE `progression`
  ADD CONSTRAINT `FK_D5B25073591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `FK_D5B25073A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_D5B25073CDF80196` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
